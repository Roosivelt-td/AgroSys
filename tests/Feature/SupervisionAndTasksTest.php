<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\AsignacionSupervisor;
use App\Models\SugerenciaTarea;
use App\Models\Terreno;
use App\Models\Cultivo;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupervisionAndTasksTest extends TestCase
{
    use DatabaseTransactions;

    protected $rolAgricultor;
    protected $rolAdminOrg;
    protected $rolSupervisorOrg;
    protected $rolAgricultorOrg;

    protected function setUp(): void
    {
        parent::setUp();

        // Asegurar que los roles de sistema y de organización existen
        $this->rolAgricultor = Rol::firstOrCreate(['nombre' => 'Agricultor']);

        $this->rolAdminOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Admin de Org']);
        $this->rolSupervisorOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Supervisor'], ['descripcion' => 'Supervisor de Org']);
        $this->rolAgricultorOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Agricultor'], ['descripcion' => 'Agricultor de Org']);
    }

    /**
     * Test 1: Verificar que el Administrador de Org puede ver la gestión de miembros y asignar un supervisor.
     */
    public function test_admin_can_render_gestion_miembros_and_assign_supervisor(): void
    {
        $org = Organizacion::create([
            'nombre' => 'Org Test Supervision ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);

        $adminUser = User::create([
            'nombres' => 'Admin', 'apellidos' => 'Test', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'admin.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id
        ]);

        $supUser = User::create([
            'nombres' => 'Supervisor', 'apellidos' => 'Test', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'sup.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id
        ]);

        $agriUser = User::create([
            'nombres' => 'Agricultor', 'apellidos' => 'Test', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'agri.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id
        ]);

        // Membresías
        $adminMiembro = MiembroOrganizacion::create(['usuario_id' => $adminUser->id, 'organizacion_id' => $org->id, 'es_propietario' => 1, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $adminMiembro->id, 'rol_id' => $this->rolAdminOrg->id, 'estado' => 1]);

        $supMiembro = MiembroOrganizacion::create(['usuario_id' => $supUser->id, 'organizacion_id' => $org->id, 'es_propietario' => 0, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $supMiembro->id, 'rol_id' => $this->rolSupervisorOrg->id, 'estado' => 1]);

        $agriMiembro = MiembroOrganizacion::create(['usuario_id' => $agriUser->id, 'organizacion_id' => $org->id, 'es_propietario' => 0, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $agriMiembro->id, 'rol_id' => $this->rolAgricultorOrg->id, 'estado' => 1]);

        // Probar renderizado Livewire
        Livewire::actingAs($adminUser)
            ->test(\App\Livewire\Admin\GestionMiembros::class, ['id' => $org->id])
            ->assertStatus(200)
            ->assertSee('Miembros de la Organización')
            ->call('openAssignSupervisorModal', $agriMiembro->id)
            ->set('selectedSupervisorId', $supMiembro->id)
            ->call('guardarAsignacionSupervisor');

        // Verificar que la asignación se guardó en la base de datos
        $this->assertDatabaseHas('asignaciones_supervisor', [
            'organizacion_id' => $org->id,
            'supervisor_miembro_id' => $supMiembro->id,
            'agricultor_usuario_id' => $agriUser->id,
        ]);
    }

    /**
     * Test 2: Verificar que el Supervisor puede enviar una sugerencia técnica al agricultor.
     */
    public function test_supervisor_can_send_suggestion_to_agricultor(): void
    {
        $org = Organizacion::create(['nombre' => 'Org Suggestion ' . rand(100, 999), 'ruc' => '20' . rand(100000000, 999999999), 'estado' => 1]);

        $supUser = User::create(['nombres' => 'Supervisor', 'apellidos' => 'Dos', 'dni' => (string)rand(10000000, 99999999), 'email' => 'sup2.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);
        $agriUser = User::create(['nombres' => 'Agricultor', 'apellidos' => 'Dos', 'dni' => (string)rand(10000000, 99999999), 'email' => 'agri2.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);

        $supMiembro = MiembroOrganizacion::create(['usuario_id' => $supUser->id, 'organizacion_id' => $org->id, 'es_propietario' => 0, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $supMiembro->id, 'rol_id' => $this->rolSupervisorOrg->id, 'estado' => 1]);

        $agriMiembro = MiembroOrganizacion::create(['usuario_id' => $agriUser->id, 'organizacion_id' => $org->id, 'es_propietario' => 0, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $agriMiembro->id, 'rol_id' => $this->rolAgricultorOrg->id, 'estado' => 1]);

        AsignacionSupervisor::create([
            'organizacion_id' => $org->id,
            'supervisor_miembro_id' => $supMiembro->id,
            'agricultor_usuario_id' => $agriUser->id,
        ]);

        Livewire::actingAs($supUser)
            ->test(\App\Livewire\Admin\AgricultorSupervisionDetail::class, ['id' => $agriUser->id])
            ->assertStatus(200)
            ->assertSee($agriUser->nombres)
            ->call('openSugerenciaModal')
            ->set('tituloSugerencia', 'Aplicar Riego Preventivo')
            ->set('descripcionSugerencia', 'Realizar riego de 2 horas por altas temperaturas.')
            ->set('fechaSugerida', now()->format('Y-m-d'))
            ->call('enviarSugerencia');

        // Verificar que la sugerencia se guardó
        $this->assertDatabaseHas('sugerencias_tareas', [
            'organizacion_id' => $org->id,
            'supervisor_usuario_id' => $supUser->id,
            'agricultor_usuario_id' => $agriUser->id,
            'titulo' => 'Aplicar Riego Preventivo',
            'estado' => 0,
        ]);
    }

    /**
     * Test 3: Verificar que el Agricultor puede ver la sugerencia y marcarla como completada.
     */
    public function test_agricultor_can_view_and_complete_suggestion(): void
    {
        $org = Organizacion::create(['nombre' => 'Org Complete ' . rand(100, 999), 'ruc' => '20' . rand(100000000, 999999999), 'estado' => 1]);

        $supUser = User::create(['nombres' => 'Supervisor', 'apellidos' => 'Tres', 'dni' => (string)rand(10000000, 99999999), 'email' => 'sup3.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);
        $agriUser = User::create(['nombres' => 'Agricultor', 'apellidos' => 'Tres', 'dni' => (string)rand(10000000, 99999999), 'email' => 'agri3.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);

        $sugerencia = SugerenciaTarea::create([
            'organizacion_id' => $org->id,
            'supervisor_usuario_id' => $supUser->id,
            'agricultor_usuario_id' => $agriUser->id,
            'titulo' => 'Fertilizar con Potasio',
            'descripcion' => 'Aplicar 10kg por ha.',
            'fecha_sugerida' => now()->format('Y-m-d'),
            'estado' => 0,
        ]);

        Livewire::actingAs($agriUser)
            ->test(\App\Livewire\Ia\Alertas::class)
            ->assertStatus(200)
            ->call('verSugerencia', $sugerencia->id)
            ->set('comentarioRespuesta', 'Fertilización aplicada a las 7 AM.')
            ->call('completarSugerencia');

        // Verificar estado actualizado en la BD
        $this->assertDatabaseHas('sugerencias_tareas', [
            'id' => $sugerencia->id,
            'estado' => 1,
            'comentario_agricultor' => 'Fertilización aplicada a las 7 AM.',
        ]);
    }

    /**
     * Test 4: Verificar que la mensajería abre directamente la conversación privada con el usuario.
     */
    public function test_chat_manager_opens_direct_conversation_with_user(): void
    {
        $supUser = User::create(['nombres' => 'Supervisor', 'apellidos' => 'Cuatro', 'dni' => (string)rand(10000000, 99999999), 'email' => 'sup4.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);
        $agriUser = User::create(['nombres' => 'Agricultor', 'apellidos' => 'Cuatro', 'dni' => (string)rand(10000000, 99999999), 'email' => 'agri4.' . rand(100,999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $this->rolAgricultor->id]);

        Livewire::actingAs($supUser)
            ->withQueryParams(['user' => $agriUser->id])
            ->test(\App\Livewire\Chat\ChatManager::class)
            ->assertStatus(200);
    }
}
