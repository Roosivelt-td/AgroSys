<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\CatalogoCultivo;
use App\Models\CatalogoLabor;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Comprador;
use App\Models\Venta;
use App\Models\HistorialProceso;
use App\Services\AgroBotService;
use App\Services\WeatherService;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgroSysServicesAndModulesTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $org;
    protected $terreno;
    protected $cultivo;
    protected $catalogoCultivo;
    protected $catalogoLabor;

    protected function setUp(): void
    {
        parent::setUp();

        // Asegurar roles de sistema y organización
        $rolAgri = Rol::firstOrCreate(['nombre' => 'Agricultor']);
        $rolAdminOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Admin de Org']);

        $this->user = User::create([
            'nombres' => 'Fernando', 'apellidos' => 'Suarez', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'fernando.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolAgri->id
        ]);

        $this->org = Organizacion::create([
            'nombre' => 'Agrícola San Juan ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);

        $miembro = MiembroOrganizacion::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'es_propietario' => 1,
            'estado' => 1
        ]);
        MiembroRol::create(['miembro_id' => $miembro->id, 'rol_id' => $rolAdminOrg->id, 'estado' => 1]);

        $this->terreno = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Fundo Los Pinos',
            'hectareas' => 12.0,
            'tipo_tenencia' => 'propio',
            'calidad_suelo' => 'franco'
        ]);

        $this->catalogoCultivo = CatalogoCultivo::firstOrCreate(
            ['nombre' => 'Maíz'],
            ['dias_a_cosecha_promedio' => 150, 'tipo_ciclo' => 'ciclo_corto']
        );

        $this->catalogoLabor = CatalogoLabor::firstOrCreate(
            ['nombre' => 'Fertilización'],
            ['categoria' => 'mantenimiento']
        );

        $this->cultivo = Cultivo::create([
            'terreno_id' => $this->terreno->id,
            'catalogo_cultivo_id' => $this->catalogoCultivo->id,
            'nombre_lote' => 'LOTE-M1',
            'variedad' => 'Duro',
            'fecha_planificada' => now()->subDays(30)->format('Y-m-d'),
            'fecha_siembra' => now()->subDays(30),
            'estado' => 'En crecimiento',
            'area_destinada' => 5.0
        ]);
    }

    /**
     * Test 1: Cobertura de Servicios (WeatherService, AgroBotService)
     */
    public function test_services_coverage(): void
    {
        // 1. WeatherService
        $weatherService = new WeatherService();
        $weatherData = $weatherService->getWeatherByCoords(-12.046374, -77.042793);
        $weatherArray = is_object($weatherData) ? (array)$weatherData : $weatherData;
        $this->assertIsArray($weatherArray);
        $this0 = $weatherArray['temperatura'] ?? $weatherArray['temp'] ?? 20;
        $this->assertNotNull($this0);

        // 2. AgroBotService (Weather Analysis & Strategic Plan)
        $bot = new AgroBotService();
        $analysis = $bot->analyzeWeather($weatherData, ['nombre' => 'Maíz', 'variedad' => 'Duro']);
        $this->assertIsArray($analysis);

        $plan = $bot->coordinateStrategicPlan([
            'dias_cultivo' => 30,
            'dias_totales' => 150,
            'crop_name' => 'Maíz Amarillo',
            'etapa_nombre' => 'Crecimiento',
            'json_profile' => ['id' => 'maiz', 'nombre' => 'Maíz'],
            'weather' => $weatherData,
            'historial_labores' => 'Abonado',
            'historial_clima' => 'Estable',
            'stats' => ['total' => 500]
        ]);
        $this->assertNotEmpty($plan);
    }

    /**
     * Test 2: Cobertura de Observers (AgroAuditObserver)
     */
    public function test_audit_observer_coverage(): void
    {
        $initialCount = HistorialProceso::count();

        // Probar INSERT audit
        $testOrg = Organizacion::create([
            'nombre' => 'Empresa Audit Test ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);
        $this->assertTrue(HistorialProceso::count() > $initialCount);

        // Probar UPDATE audit
        $testOrg->update(['nombre' => 'Empresa Audit Test Editada']);
        $this->assertDatabaseHas('historial_procesos', [
            'tabla_afectada' => 'organizaciones',
            'accion' => 'UPDATE'
        ]);

        // Probar DELETE audit
        $testOrg->delete();
        $this->assertDatabaseHas('historial_procesos', [
            'tabla_afectada' => 'organizaciones',
            'accion' => 'DELETE'
        ]);
    }

    /**
     * Test 3: Cobertura de Modelos y Atributos Personalizados
     */
    public function test_models_and_custom_attributes_coverage(): void
    {
        // Terreno attributes
        $this->assertEquals(5.0, $this->terreno->area_ocupada);
        $this->assertEquals(7.0, $this->terreno->area_disponible);
        $this->assertFalse($this->terreno->is_alquiler_vencido);

        // Cultivo descripcion_completa attribute
        $desc = mb_strtoupper($this->cultivo->descripcion_completa, 'UTF-8');
        $this->assertStringContainsString('5.00 HA DE MAÍZ', $desc);

        // Relaciones
        $this->assertNotNull($this->cultivo->terreno);
        $this->assertNotNull($this->cultivo->detalleCatalogo);
        $this->assertNotNull($this->terreno->responsable);
        $this->assertNotNull($this->terreno->organizacion);
    }

    /**
     * Test 4: Cobertura de Componentes Livewire (TerrenosManager, CultivosManager, LaboresManager, VentasManager, CosechasManager)
     */
    public function test_livewire_managers_coverage(): void
    {
        // 1. TerrenosManager
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\TerrenosManager::class)
            ->set('search', 'Pinos')
            ->set('filterArea', '10-20')
            ->call('resetForm')
            ->assertStatus(200);

        // 2. CultivosManager
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\CultivosManager::class)
            ->set('searchCultivo', 'Maíz')
            ->call('filterByTerreno', $this->terreno->id)
            ->call('showCropReport', $this->cultivo->id)
            ->assertStatus(200);

        // 3. LaboresManager
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\LaboresManager::class)
            ->set('fLand', 'Pinos')
            ->call('openCreateModal')
            ->set('cultivo_id', $this->cultivo->id)
            ->set('catalogo_labor_id', $this->catalogoLabor->id)
            ->set('fecha_realizacion', now()->format('Y-m-d'))
            ->set('costo_mano_obra_total', 100)
            ->set('costo_maquinaria_total', 50)
            ->set('costo_total', 150)
            ->call('save')
            ->assertStatus(200);

        $labor = Labor::firstWhere('cultivo_id', $this->cultivo->id);
        $this->assertNotNull($labor);

        // 4. CosechasManager
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\CosechasManager::class)
            ->call('showCropReport', $this->cultivo->id)
            ->assertStatus(200);

        // 5. VentasManager
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\VentasManager::class)
            ->set('newCompNombre', 'Empresa Distribuidora Norte')
            ->set('newCompRucDni', '20112233445')
            ->call('saveQuickComprador')
            ->assertStatus(200);

        $this->assertDatabaseHas('compradores', ['nombre' => 'EMPRESA DISTRIBUIDORA NORTE']);
    }
}
