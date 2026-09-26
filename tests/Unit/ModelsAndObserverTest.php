<?php

namespace Tests\Unit;

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
use App\Models\Venta;
use App\Models\Comprador;
use App\Models\SugerenciaTarea;
use App\Models\HistorialProceso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModelsAndObserverTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();

        $rolAgri = Rol::firstOrCreate(['nombre' => 'Agricultor']);
        $this->user = User::create([
            'nombres' => 'Audit', 'apellidos' => 'Tester', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'audit.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolAgri->id
        ]);

        $this->org = Organizacion::create([
            'nombre' => 'Org Audit Test ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);
    }

    /**
     * Test AgroAuditObserver creates audit log when models are created/updated/deleted.
     */
    public function test_observer_records_historial_procesos(): void
    {
        $this->actingAs($this->user);

        $terreno = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Fundo Audit',
            'hectareas' => 3.5,
            'tipo_tenencia' => 'propio'
        ]);

        // Verificar que el observador registró la creación en historial_procesos
        $this->assertDatabaseHas('historial_procesos', [
            'usuario_id' => $this->user->id,
            'tabla_afectada' => 'terrenos',
            'registro_id' => $terreno->id,
            'accion' => 'INSERT'
        ]);

        // Actualizar terreno
        $terreno->update(['nombre' => 'Fundo Audit Actualizado']);

        $this->assertDatabaseHas('historial_procesos', [
            'usuario_id' => $this->user->id,
            'tabla_afectada' => 'terrenos',
            'registro_id' => $terreno->id,
            'accion' => 'UPDATE'
        ]);
    }

    /**
     * Test Eloquent Relationships between Terreno, Cultivo, Labor, Cosecha, Venta.
     */
    public function test_eloquent_relationships_chain(): void
    {
        $terreno = Terreno::create(['usuario_id' => $this->user->id, 'organizacion_id' => $this->org->id, 'nombre' => 'Parcela Model', 'hectareas' => 5.0, 'tipo_tenencia' => 'propio']);
        $catCultivo = CatalogoCultivo::firstOrCreate(['nombre' => 'Quinua Real'], ['dias_a_cosecha_promedio' => 150, 'tipo_ciclo' => 'ciclo_corto']);
        $cultivo = Cultivo::create([
            'terreno_id' => $terreno->id,
            'catalogo_cultivo_id' => $catCultivo->id,
            'nombre_lote' => 'LOTE-M1',
            'variedad' => 'Roja',
            'fecha_planificada' => now()->format('Y-m-d'),
            'fecha_siembra' => now()->format('Y-m-d'),
            'area_destinada' => 2.0,
            'estado' => 'En crecimiento'
        ]);

        $catLabor = CatalogoLabor::firstOrCreate(['nombre' => 'Aporque Secundario'], ['categoria' => 'mantenimiento']);
        $labor = Labor::create(['cultivo_id' => $cultivo->id, 'catalogo_labor_id' => $catLabor->id, 'fecha_realizacion' => now(), 'costo_total' => 120.00, 'estado' => 'Completada']);

        $cosecha = Cosecha::create(['labor_id' => $labor->id, 'fecha_cosecha' => now(), 'cantidad_kg' => 1500.00, 'unidad_medida' => 'kg', 'calidad' => 'primera', 'lote_codigo' => 'COS-M1']);
        $comprador = Comprador::create(['nombre' => 'Comprador Quinua', 'ruc_dni' => '20112233445']);
        $venta = Venta::create([
            'cosecha_id' => $cosecha->id,
            'comprador_id' => $comprador->id,
            'fecha_venta' => now()->format('Y-m-d'),
            'cantidad_vendida_kg' => 1000.00,
            'precio_por_kg' => 8.50,
            'costo_flete' => 0.00,
            'impuestos' => 0.00,
            'comprobante_tipo' => 'factura'
        ]);

        // Assertions de Relaciones Eloquent
        $this->assertEquals($terreno->id, $cultivo->terreno->id);
        $this->assertEquals($cultivo->id, $labor->cultivo->id);
        $this->assertEquals($labor->id, $cosecha->labor->id);
        $this->assertEquals($cosecha->id, $venta->cosecha->id);
        $this->assertEquals($comprador->id, $venta->comprador->id);
    }
}
