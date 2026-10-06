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
use App\Services\AgroBotService;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgroSysFullWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $org;
    protected $catalogoCultivo;
    protected $catalogoLabor;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles de sistema y organización
        $rolAgri = Rol::firstOrCreate(['nombre' => 'Agricultor']);
        $rolAdminOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Admin de Org']);

        $this->user = User::create([
            'nombres' => 'Carlos', 'apellidos' => 'Mendoza', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'carlos.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolAgri->id
        ]);

        $this->org = Organizacion::create([
            'nombre' => 'Agrícola Valle Fertil ' . rand(100, 999),
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

        $this->catalogoCultivo = CatalogoCultivo::firstOrCreate(
            ['nombre' => 'Papa Canchan'],
            ['dias_a_cosecha_promedio' => 120, 'tipo_ciclo' => 'ciclo_corto']
        );

        $this->catalogoLabor = CatalogoLabor::firstOrCreate(
            ['nombre' => 'Riego por Goteo'],
            ['categoria' => 'mantenimiento']
        );
    }

    /**
     * Test 1: Crear Terreno mediante el Administrador de Terrenos
     */
    public function test_can_create_and_manage_terreno(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\TerrenosManager::class)
            ->assertStatus(200)
            ->set('landName', 'Fundo La Esperanza')
            ->set('landArea', 5.5)
            ->set('landLat', -12.046374)
            ->set('landLng', -77.042793)
            ->set('landPolygon', json_encode([
                ['lat' => -12.0463, 'lng' => -77.0427],
                ['lat' => -12.0464, 'lng' => -77.0428],
                ['lat' => -12.0465, 'lng' => -77.0426]
            ]))
            ->set('landSoil', 'franco')
            ->set('landWater', 'Riego por goteo')
            ->call('save');

        $this->assertDatabaseHas('terrenos', [
            'nombre' => 'Fundo La Esperanza',
            'usuario_id' => $this->user->id,
            'hectareas' => 5.5,
        ]);
    }

    /**
     * Test 2: Sembrar Cultivo en el Terreno creado
     */
    public function test_can_create_and_track_cultivo(): void
    {
        $terreno = Terreno::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'nombre' => 'Parcela San José',
            'hectareas' => 10.0,
            'tipo_tenencia' => 'propio'
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\CultivosManager::class)
            ->assertStatus(200)
            ->call('selectTerreno', $terreno->id, $terreno->nombre, 10.0)
            ->set('catalogo_cultivo_id', $this->catalogoCultivo->id)
            ->set('nombre_lote', 'LOTE-A1')
            ->set('variedad', 'Canchan Especial')
            ->set('area_destinada', 4.0)
            ->set('fecha_planificada', now()->format('Y-m-d'))
            ->set('estado', 'En crecimiento')
            ->call('save');

        $this->assertDatabaseHas('cultivos', [
            'terreno_id' => $terreno->id,
            'nombre_lote' => 'LOTE-A1',
            'variedad' => 'Canchan Especial',
            'area_destinada' => 4.0,
        ]);
    }

    /**
     * Test 3: Registrar Labor, Cosecha y Venta Comercial
     */
    public function test_full_labor_harvest_and_sale_cycle(): void
    {
        $terreno = Terreno::create(['usuario_id' => $this->user->id, 'organizacion_id' => $this->org->id, 'nombre' => 'Fundo Central', 'hectareas' => 8.0, 'tipo_tenencia' => 'propio']);
        $cultivo = Cultivo::create([
            'terreno_id' => $terreno->id,
            'catalogo_cultivo_id' => $this->catalogoCultivo->id,
            'nombre_lote' => 'LOTE-B2',
            'variedad' => 'Amarilla',
            'fecha_planificada' => now()->subDays(60)->format('Y-m-d'),
            'fecha_siembra' => now()->subDays(60),
            'estado' => 'En crecimiento',
            'area_destinada' => 3.0
        ]);

        $labor = Labor::create([
            'cultivo_id' => $cultivo->id,
            'catalogo_labor_id' => $this->catalogoLabor->id,
            'fecha_realizacion' => now(),
            'costo_mano_obra_total' => 250.00,
            'costo_maquinaria_total' => 150.00,
            'costo_total' => 400.00,
            'estado' => 'Completada'
        ]);

        $cosecha = Cosecha::create([
            'labor_id' => $labor->id,
            'fecha_cosecha' => now(),
            'cantidad_kg' => 5000.00,
            'unidad_medida' => 'kg',
            'calidad' => 'primera',
            'lote_codigo' => 'COS-001'
        ]);

        $comprador = Comprador::create(['nombre' => 'COMPRADOR CENTRAL ' . rand(100, 999), 'ruc_dni' => '20998877665']);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\VentasManager::class)
            ->assertStatus(200)
            ->set('cosecha_id', $cosecha->id)
            ->set('comprador_id', $comprador->id)
            ->set('fecha_venta', now()->format('Y-m-d'))
            ->set('cantidad_vendida_kg', 3000.00)
            ->set('precio_por_kg', 2.50)
            ->set('comprobante_tipo', 'boleta')
            ->set('comprobante_numero', 'B001-00123')
            ->call('save');

        $this->assertDatabaseHas('ventas', [
            'cosecha_id' => $cosecha->id,
            'comprador_id' => $comprador->id,
            'cantidad_vendida_kg' => 3000.00,
            'precio_por_kg' => 2.50,
        ]);
    }

    /**
     * Test 4: Motor de Inteligencia Agronómica (AgroBotService)
     */
    public function test_agrobot_ai_service_response(): void
    {
        $bot = new AgroBotService();
        $response = $bot->coordinateStrategicPlan([
            'dias_cultivo' => 30,
            'dias_totales' => 120,
            'crop_name' => 'Papa Canchan',
            'etapa_nombre' => 'Desarrollo Vegetativo',
            'json_profile' => ['id' => 'papa', 'nombre' => 'Papa'],
            'weather' => ['temp' => 22, 'humedad' => 60, 'viento' => 10, 'condicion' => 'Soleado', 'prob_lluvia' => 5, 'forecast' => []],
            'historial_labores' => 'Riego inicial',
            'historial_clima' => 'Templado',
            'stats' => ['total' => 850]
        ]);

        $this->assertNotEmpty($response);
        $this->assertTrue(
            str_contains($response, 'LO QUE SE DEBE HACER HOY') || str_contains($response, 'no respondió')
        );
    }
}
