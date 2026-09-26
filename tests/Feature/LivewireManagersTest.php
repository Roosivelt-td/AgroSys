<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\Notificacion;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LivewireManagersTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $org;
    protected $rolAdminOrg;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Rol::firstOrCreate(['nombre' => 'Agricultor']);
        $this->rolAdminOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Admin de Org']);

        $this->user = User::create([
            'nombres' => 'Livewire', 'apellidos' => 'Tester', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'livewire.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rol->id
        ]);

        $this->org = Organizacion::create([
            'nombre' => 'Org Livewire Test ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'estado' => 1
        ]);

        $miembro = MiembroOrganizacion::create([
            'usuario_id' => $this->user->id,
            'organizacion_id' => $this->org->id,
            'es_propietario' => 1,
            'estado' => 1
        ]);
        MiembroRol::create(['miembro_id' => $miembro->id, 'rol_id' => $this->rolAdminOrg->id, 'estado' => 1]);
    }

    /**
     * Test NotificationCenter component.
     */
    public function test_notification_center_renders_and_marks_notifications_read(): void
    {
        $notif = Notificacion::create([
            'usuario_id' => $this->user->id,
            'titulo' => 'Alerta de Prueba Livewire',
            'mensaje' => 'Prueba de notificación.',
            'tipo' => 'informativa',
            'leido' => 0
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Layout\NotificationCenter::class)
            ->assertStatus(200)
            ->assertSee('Alerta de Prueba Livewire')
            ->call('markAsRead', $notif->id);

        $this->assertDatabaseHas('notificaciones', [
            'id' => $notif->id,
            'leido' => 1
        ]);
    }

    /**
     * Test CosechasManager renders successfully.
     */
    public function test_cosechas_manager_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\CosechasManager::class)
            ->assertStatus(200);
    }

    /**
     * Test LaboresManager renders successfully.
     */
    public function test_labores_manager_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\LaboresManager::class)
            ->assertStatus(200);
    }

    /**
     * Test VentasManager renders successfully.
     */
    public function test_ventas_manager_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Admin\VentasManager::class)
            ->assertStatus(200);
    }
}
