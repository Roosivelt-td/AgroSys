<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\CatalogoCultivo;
use App\Models\CatalogoLabor;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Comprador;
use App\Models\Venta;
use App\Models\HistorialProceso;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgroSysNewModulesIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();

        $rolAgri = Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);
        $this->user = User::create([
            'nombres' => 'Roosivelt', 'apellidos' => 'Tester', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'new.modules.' . rand(100, 999) . '@agrosys.com', 'password' => 'password', 'rol_id' => $rolAgri->id, 'is_activo' => true
        ]);

        $this->org = Organizacion::create([
            'nombre' => 'Org Integration Test ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);

        MiembroOrganizacion::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'es_propietario' => 1,
            'estado' => 1
        ]);
    }

    /**
     * Prueba 1: Terrenos Alquilados Vencidos, Inhabilitación de Siembras y Renovación de Contrato.
     */
    public function test_rented_terreno_expiration_and_renewal_flow(): void
    {
        $this->actingAs($this->user);

        // Terreno alquilado con fecha de vencimiento pasada
        $terrenoAlquilado = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Fundo Contrato Vencido',
            'hectareas' => 5.0,
            'tipo_tenencia' => 'alquilado',
            'costo_alquiler_anual' => 1500.00,
            'alquiler_modalidad' => 'global',
            'alquiler_periodo' => 'fecha',
            'fecha_alquiler' => now()->subYear(),
            'fecha_vencimiento_alquiler' => now()->subDay(),
        ]);

        // Verificaciones de estado inhabilitado
        $this->assertTrue($terrenoAlquilado->is_alquiler_vencido);
        $this->assertFalse($terrenoAlquilado->is_habilitado_para_cultivo);
        $this->assertEquals(0, $terrenoAlquilado->area_disponible);

        // Intentar registrar siembra en terreno vencido debe fallar en la validación
        $catCultivo = CatalogoCultivo::firstOrCreate(['nombre' => 'Maíz Test'], ['dias_a_cosecha_promedio' => 120, 'tipo_ciclo' => 'ciclo_corto']);

        Livewire::test(\App\Livewire\Admin\CultivosManager::class)
            ->call('selectTerreno', $terrenoAlquilado->id, $terrenoAlquilado->nombre, 0.0)
            ->set('catalogo_cultivo_id', $catCultivo->id)
            ->set('nombre_lote', 'LOTE-X')
            ->set('area_destinada', 2.0)
            ->set('fecha_planificada', now()->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['terreno_id']);

        // Renovar el alquiler mediante TerrenosManager
        Livewire::test(\App\Livewire\Admin\TerrenosManager::class)
            ->call('openRenewModal', $terrenoAlquilado->id)
            ->set('renewFechaAlquiler', now()->format('Y-m-d'))
            ->set('renewFechaVencimiento', now()->addYear()->format('Y-m-d'))
            ->set('renewCostoAnual', 2000.00)
            ->call('renewAlquiler')
            ->assertHasNoErrors();

        // Verificar que el terreno se actualizó y volvió a estar habilitado
        $terrenoAlquilado->refresh();
        $this->assertFalse($terrenoAlquilado->is_alquiler_vencido);
        $this->assertTrue($terrenoAlquilado->is_habilitado_para_cultivo);
        $this->assertEquals(5.0, $terrenoAlquilado->area_disponible);
    }

    /**
     * Prueba 2: Nombre de Lote Autogenerado y Carga de Evidencia Fotográfica en Cultivo.
     */
    public function test_cultivo_auto_lote_code_and_image_upload(): void
    {
        $this->actingAs($this->user);

        $terreno = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Fundo Siembra Test',
            'hectareas' => 10.0,
            'tipo_tenencia' => 'propio'
        ]);

        $catCultivo = CatalogoCultivo::firstOrCreate(['nombre' => 'Papa Test'], ['dias_a_cosecha_promedio' => 130, 'tipo_ciclo' => 'ciclo_corto']);

        $image = UploadedFile::fake()->create('evidencia_siembra.jpg', 200, 'image/jpeg');

        Livewire::test(\App\Livewire\Admin\CultivosManager::class)
            ->call('openCreateModal')
            ->assertSet('nombre_lote', fn($val) => strlen($val) === 4) // Verifica que se autogenera un código de 4 letras
            ->call('selectTerreno', $terreno->id, $terreno->nombre, 10.0)
            ->set('catalogo_cultivo_id', $catCultivo->id)
            ->set('area_destinada', 3.5)
            ->set('fecha_planificada', now()->format('Y-m-d'))
            ->set('cropPhoto', $image)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cultivos', [
            'terreno_id' => $terreno->id,
            'catalogo_cultivo_id' => $catCultivo->id,
            'area_destinada' => 3.5
        ]);
    }

    /**
     * Prueba 3: Búsqueda y Validación Inteligente de Compradores Duplicados.
     */
    public function test_comprador_duplicate_validation(): void
    {
        $this->actingAs($this->user);

        // Registrar primer comprador
        Livewire::test(\App\Livewire\Admin\VentasManager::class)
            ->set('newCompNombre', 'Comercializadora del Sur')
            ->set('newCompDir', 'Av. Parra 123, Arequipa')
            ->call('saveQuickComprador')
            ->assertHasNoErrors();

        // Registrar segundo comprador idéntico en nombre y dirección (debe dar error por duplicado)
        Livewire::test(\App\Livewire\Admin\VentasManager::class)
            ->set('newCompNombre', 'Comercializadora del Sur')
            ->set('newCompDir', 'Av. Parra 123, Arequipa')
            ->call('saveQuickComprador')
            ->assertHasErrors(['newCompNombre']);

        // Registrar tercer comprador con el mismo nombre pero diferente ubicación (debe permitirse)
        Livewire::test(\App\Livewire\Admin\VentasManager::class)
            ->set('newCompNombre', 'Comercializadora del Sur')
            ->set('newCompDir', 'Mercado Central Lote 4, Cusco')
            ->call('saveQuickComprador')
            ->assertHasNoErrors();

        $this->assertEquals(2, Comprador::where('nombre', 'COMERCIALIZADORA DEL SUR')->count());
    }

    /**
     * Prueba 4: Historial de Auditoría con Explicación Humana Inteligente de Cambios.
     */
    public function test_audit_history_ai_change_explanation(): void
    {
        $this->actingAs($this->user);

        $terreno = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Fundo Audit Explicación',
            'hectareas' => 4.0,
            'tipo_tenencia' => 'alquilado',
            'costo_alquiler_anual' => 1200.00
        ]);

        // Actualizar datos del terreno
        $terreno->update([
            'hectareas' => 6.5,
            'tipo_tenencia' => 'propio'
        ]);

        $log = HistorialProceso::where('tabla_afectada', 'terrenos')
            ->where('registro_id', $terreno->id)
            ->where('accion', 'EDICIÓN TERRENO')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);

        // Verificar el generador de explicación comparativa de cambios
        $analysis = $log->explicacion_cambios;
        $this->assertIsArray($analysis);
        $this->assertNotEmpty($analysis['cambios']);

        $camposCambio = collect($analysis['cambios'])->pluck('campo')->toArray();
        $this->assertContains('Área / Hectáreas', $camposCambio);
        $this->assertContains('Tipo de Tenencia', $camposCambio);
    }
}
