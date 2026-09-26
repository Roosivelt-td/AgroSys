<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\Solicitud;
use App\Models\AsignacionSupervisor;
use App\Http\Controllers\OrganizacionController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrganizacionControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $userAdmin;
    protected $userAgri;
    protected $superAdmin;
    protected $rolAdminOrg;
    protected $rolSupervisorOrg;
    protected $rolAgricultorOrg;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $rolAgri = Rol::firstOrCreate(['nombre' => 'Agricultor']);
        $rolSA = Rol::firstOrCreate(['nombre' => 'Super Admin']);

        $this->superAdmin = User::firstOrCreate(['id' => 1], [
            'nombres' => 'Super', 'apellidos' => 'Admin', 'dni' => '00000001',
            'email' => 'sa@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolSA->id
        ]);

        $this->rolAdminOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Admin de Org']);
        $this->rolSupervisorOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Supervisor'], ['descripcion' => 'Supervisor de Org']);
        $this->rolAgricultorOrg = RolesOrganizacion::firstOrCreate(['nombre' => 'Agricultor'], ['descripcion' => 'Agricultor de Org']);

        $this->userAdmin = User::create([
            'nombres' => 'OrgAdmin', 'apellidos' => 'Tester', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'orgadmin.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolAgri->id
        ]);

        $this->userAgri = User::create([
            'nombres' => 'OrgAgri', 'apellidos' => 'Tester', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'orgagri.' . rand(100, 999) . '@agrosys.com', 'password' => bcrypt('password'), 'rol_id' => $rolAgri->id
        ]);

        $this->controller = new OrganizacionController();
    }

    /**
     * Test registrar y aprobar solicitud de organización.
     */
    public function test_registrar_y_aprobar_organizacion(): void
    {
        $this->actingAs($this->userAdmin);

        $resReg = $this->controller->registrar([
            'nombre' => 'Empresa Test Controller ' . rand(100, 999),
            'ruc' => '20' . rand(100000000, 999999999),
            'descripcion' => 'Empresa de prueba para controlador'
        ]);

        $this->assertTrue($resReg['success']);

        $solicitud = Solicitud::where('solicitante_usuario_id', $this->userAdmin->id)->latest()->first();
        $this->assertNotNull($solicitud);

        $resAprobar = $this->controller->aprobarSolicitud($solicitud->id);
        $this->assertTrue($resAprobar['success']);

        $this->assertDatabaseHas('organizaciones', ['nombre' => $solicitud->datos_extra['nombre']]);
    }

    /**
     * Test invitar usuario a una organización existente.
     */
    public function test_invitar_usuario_y_aprobar_ingreso(): void
    {
        $this->actingAs($this->userAdmin);

        $org = Organizacion::create(['nombre' => 'Org Invitar ' . rand(100, 999), 'ruc' => '20' . rand(100000000, 999999999), 'estado' => 1]);
        $miembroAdmin = MiembroOrganizacion::create(['usuario_id' => $this->userAdmin->id, 'organizacion_id' => $org->id, 'es_propietario' => 1, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $miembroAdmin->id, 'rol_id' => $this->rolAdminOrg->id, 'estado' => 1]);

        $resInv = $this->controller->invitarUsuario($org->id, $this->userAgri->dni, $this->rolAgricultorOrg->id);
        $this->assertTrue($resInv['success']);

        $solicitudInv = Solicitud::where('destinatario_usuario_id', $this->userAgri->id)->latest()->first();
        $this->assertNotNull($solicitudInv);

        $this->actingAs($this->userAgri);
        $resAprobar = $this->controller->aprobarIngresoMiembro($solicitudInv->id);
        $this->assertTrue($resAprobar['success']);

        $this->assertDatabaseHas('miembros_organizacion', [
            'usuario_id' => $this->userAgri->id,
            'organizacion_id' => $org->id,
            'estado' => 1
        ]);
    }

    /**
     * Test asignación y eliminación de supervisor.
     */
    public function test_asignar_y_eliminar_supervisor(): void
    {
        $this->actingAs($this->userAdmin);

        $org = Organizacion::create(['nombre' => 'Org Supervisor ' . rand(100, 999), 'ruc' => '20' . rand(100000000, 999999999), 'estado' => 1]);

        $supMiembro = MiembroOrganizacion::create(['usuario_id' => $this->userAdmin->id, 'organizacion_id' => $org->id, 'es_propietario' => 1, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $supMiembro->id, 'rol_id' => $this->rolSupervisorOrg->id, 'estado' => 1]);

        $agriMiembro = MiembroOrganizacion::create(['usuario_id' => $this->userAgri->id, 'organizacion_id' => $org->id, 'es_propietario' => 0, 'estado' => 1]);
        MiembroRol::create(['miembro_id' => $agriMiembro->id, 'rol_id' => $this->rolAgricultorOrg->id, 'estado' => 1]);

        $resAsig = $this->controller->asignarAgricultorASupervisor($org->id, $supMiembro->id, $this->userAgri->id);
        $this->assertTrue($resAsig['success']);

        $asig = AsignacionSupervisor::where('supervisor_miembro_id', $supMiembro->id)->first();
        $this->assertNotNull($asig);

        $resDel = $this->controller->eliminarAsignacionSupervisor($asig->id);
        $this->assertTrue($resDel['success']);

        $this->assertDatabaseMissing('asignaciones_supervisor', ['id' => $asig->id]);
    }
}
