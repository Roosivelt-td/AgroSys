<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\RolesOrganizacion;
use App\Models\MiembroRol;
use App\Models\Solicitud;
use App\Models\Notificacion;
use App\Models\HistorialProceso;
use App\Models\AsignacionSupervisor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizacionController extends Controller
{
    public function registrar(array $datos)
    {
        $usuario = Auth::user();
        return DB::transaction(function () use ($datos, $usuario) {
            $solicitud = Solicitud::create(['tipo' => 'creacion_organizacion', 'estado' => 0, 'solicitante_usuario_id' => $usuario->id, 'destinatario_usuario_id' => 1, 'datos_extra' => $datos, 'fecha_solicitud' => now()]);
            $superAdmins = User::where('rol_id', 1)->get();
            foreach ($superAdmins as $admin) {
                Notificacion::create(['usuario_id' => $admin->id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Nueva Solicitud de Organización', 'mensaje' => 'El usuario ' . $usuario->nombres . ' solicita registrar la empresa: ' . $datos['nombre'], 'tipo' => 'solicitud_pendiente']);
            }
            $this->registrarHistorial($usuario->id, null, 'solicitudes', $solicitud->id, 'SOLICITUD', 'Solicitud formal de creación: ' . $datos['nombre']);
            return ['success' => true, 'mensaje' => 'Solicitud enviada correctamente.'];
        });
    }

    public function aprobarSolicitud(int $solicitudId)
    {
        return DB::transaction(function () use ($solicitudId) {
            $solicitud = Solicitud::findOrFail($solicitudId);
            if ($solicitud->estado !== 0) return ['success' => false, 'mensaje' => 'Ya procesada.'];
            $datos = $solicitud->datos_extra;
            $organizacion = Organizacion::create(['nombre' => $datos['nombre'], 'ruc' => $datos['ruc'], 'descripcion' => $datos['descripcion'] ?? null, 'email' => $datos['email'] ?? null, 'estado' => 1]);
            $miembro = MiembroOrganizacion::updateOrCreate(['usuario_id' => $solicitud->solicitante_usuario_id, 'organizacion_id' => $organizacion->id], ['es_propietario' => 1, 'estado' => 1, 'fecha_ingreso' => now(), 'deleted_at' => null]);
            MiembroRol::updateOrCreate(['miembro_id' => $miembro->id, 'rol_id' => 1], ['estado' => 1]);
            MiembroRol::updateOrCreate(['miembro_id' => $miembro->id, 'rol_id' => 3], ['estado' => 1]);
            $solicitud->update(['estado' => 1, 'fecha_respuesta' => now(), 'organizacion_id' => $organizacion->id]);
            Notificacion::create(['usuario_id' => $solicitud->solicitante_usuario_id, 'solicitud_id' => $solicitud->id, 'titulo' => '¡Organización Aprobada!', 'mensaje' => 'Empresa activa. Eres Administrador y Agricultor.', 'tipo' => 'exito']);
            $this->registrarHistorial(Auth::id(), $organizacion->id, 'organizaciones', $organizacion->id, 'APROBACIÓN', 'Aprobación de creación de ' . $organizacion->nombre, $datos);
            return ['success' => true, 'mensaje' => 'Organización activada.'];
        });
    }

    public function rechazarSolicitud(int $solicitudId, string $motivo = '')
    {
        return DB::transaction(function () use ($solicitudId, $motivo) {
            $solicitud = Solicitud::findOrFail($solicitudId);
            $solicitud->update(['estado' => 2, 'fecha_respuesta' => now()]);
            Notificacion::create(['usuario_id' => $solicitud->solicitante_usuario_id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Solicitud Rechazada', 'mensaje' => 'Motivo: ' . $motivo, 'tipo' => 'error']);
            $this->registrarHistorial(Auth::id(), $solicitud->organizacion_id, 'solicitudes', $solicitud->id, 'RECHAZO', 'Rechazo de trámite: ' . $solicitud->tipo);
            return ['success' => true, 'mensaje' => 'Solicitud rechazada.'];
        });
    }

    public function solicitarUnirse(int $organizacionId)
    {
        $usuario = Auth::user();
        $membresiaActiva = MiembroOrganizacion::where('usuario_id', $usuario->id)->where('organizacion_id', $organizacionId)->where('estado', 1)->exists();
        if ($membresiaActiva) return ['success' => false, 'mensaje' => 'Ya eres un miembro activo.'];
        $solicitudPendiente = Solicitud::where('solicitante_usuario_id', $usuario->id)->where('organizacion_id', $organizacionId)->where('tipo', 'unirse_organizacion')->where('estado', 0)->exists();
        if ($solicitudPendiente) return ['success' => false, 'mensaje' => 'Tu solicitud ya está en proceso.'];

        return DB::transaction(function () use ($usuario, $organizacionId) {
            $organizacion = Organizacion::findOrFail($organizacionId);
            $solicitud = Solicitud::create(['tipo' => 'unirse_organizacion', 'estado' => 0, 'solicitante_usuario_id' => $usuario->id, 'organizacion_id' => $organizacionId, 'fecha_solicitud' => now()]);
            $admins = MiembroOrganizacion::where('organizacion_id', $organizacionId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->get();
            foreach ($admins as $admin) {
                Notificacion::create(['usuario_id' => $admin->usuario_id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Nueva Solicitud', 'mensaje' => $usuario->nombres . ' desea unirse.', 'tipo' => 'solicitud_pendiente']);
            }
            $this->registrarHistorial($usuario->id, $organizacionId, 'solicitudes', $solicitud->id, 'SOLICITUD', 'Petición de ingreso a ' . $organizacion->nombre);
            return ['success' => true, 'mensaje' => 'Solicitud enviada correctamente.'];
        });
    }

    public function invitarUsuario(int $organizacionId, string $dni, int $rolId = 3)
    {
        $target = User::where('dni', $dni)->first();
        if (!$target) return ['success' => false, 'mensaje' => 'Usuario no encontrado.'];

        $membresia = MiembroOrganizacion::where('usuario_id', $target->id)->where('organizacion_id', $organizacionId)->first();
        if ($membresia && $membresia->estado == 1) {
            $yaTieneRol = MiembroRol::where('miembro_id', $membresia->id)->where('rol_id', $rolId)->where('estado', 1)->exists();
            if ($yaTieneRol) return ['success' => false, 'mensaje' => 'Este usuario ya tiene este cargo asignado.'];
        }

        $invitacionPendiente = Solicitud::where('destinatario_usuario_id', $target->id)->where('organizacion_id', $organizacionId)->where('tipo', 'invitacion_organizacion')->where('datos_extra->rol_propuesto_id', $rolId)->where('estado', 0)->exists();
        if ($invitacionPendiente) return ['success' => false, 'mensaje' => 'Ya existe una invitación pendiente para este cargo.'];

        // Regla: Para ser Supervisor (2), no puede estar bajo supervisión de otro actualmente
        if ($rolId == 2) {
            $estaSiendoSupervisado = AsignacionSupervisor::where('organizacion_id', $organizacionId)
                ->where('agricultor_usuario_id', $target->id)->exists();
            if ($estaSiendoSupervisado) {
                return ['success' => false, 'mensaje' => 'No se puede ascender a Supervisor a alguien que actualmente está siendo supervisado. Primero retírelo de su equipo actual.'];
            }
        }

        return DB::transaction(function () use ($target, $organizacionId, $rolId) {
            $organizacion = Organizacion::find($organizacionId);
            $rol = RolesOrganizacion::find($rolId);
            $solicitud = Solicitud::create(['tipo' => 'invitacion_organizacion', 'estado' => 0, 'solicitante_usuario_id' => Auth::id(), 'destinatario_usuario_id' => $target->id, 'organizacion_id' => $organizacionId, 'datos_extra' => ['rol_propuesto_id' => $rolId, 'rol_nombre' => $rol->nombre], 'fecha_solicitud' => now()]);
            Notificacion::create(['usuario_id' => $target->id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Invitación de Cargo', 'mensaje' => 'Has sido invitado a ser ' . $rol->nombre . ' en ' . $organizacion->nombre, 'tipo' => 'solicitud_pendiente']);
            $this->registrarHistorial(Auth::id(), $organizacionId, 'solicitudes', $solicitud->id, 'SOLICITUD', 'Invitación a cargo ' . $rol->nombre . ' enviada a ' . $target->nombres);
            return ['success' => true, 'mensaje' => 'Invitación enviada con éxito.'];
        });
    }

    public function solicitarAscenso(int $organizacionId)
    {
        $usuarioId = Auth::id();
        $yaEsSupervisor = MiembroOrganizacion::where('usuario_id', $usuarioId)->where('organizacion_id', $organizacionId)->whereHas('roles', fn($q) => $q->where('rol_id', 2)->where('estado', 1))->exists();
        if ($yaEsSupervisor) return ['success' => false, 'mensaje' => 'Ya posees el cargo de Supervisor.'];

        $solicitudPendiente = Solicitud::where('solicitante_usuario_id', $usuarioId)->where('organizacion_id', $organizacionId)->where('tipo', 'ascenso_rol')->where('estado', 0)->exists();
        if ($solicitudPendiente) return ['success' => false, 'mensaje' => 'Tu solicitud de ascenso ya está en proceso.'];

        return DB::transaction(function () use ($organizacionId, $usuarioId) {
            $solicitud = Solicitud::create(['tipo' => 'ascenso_rol', 'estado' => 0, 'solicitante_usuario_id' => $usuarioId, 'organizacion_id' => $organizacionId, 'datos_extra' => ['rol_solicitado_id' => 2], 'fecha_solicitud' => now()]);
            $admins = MiembroOrganizacion::where('organizacion_id', $organizacionId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->get();
            foreach ($admins as $admin) {
                Notificacion::create(['usuario_id' => $admin->usuario_id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Solicitud de Supervisor', 'mensaje' => Auth::user()->nombres . ' desea ser Supervisor.', 'tipo' => 'solicitud_pendiente']);
            }
            $this->registrarHistorial($usuarioId, $organizacionId, 'solicitudes', $solicitud->id, 'SOLICITUD', 'Solicitud de cargo: Supervisor');
            return ['success' => true, 'mensaje' => 'Solicitud enviada correctamente.'];
        });
    }

    public function solicitarRenunciaSupervisor(int $organizacionId)
    {
        $usuarioId = Auth::id();
        $solicitudPendiente = Solicitud::where('solicitante_usuario_id', $usuarioId)->where('organizacion_id', $organizacionId)->where('tipo', 'renuncia_rol')->where('estado', 0)->exists();
        if ($solicitudPendiente) return ['success' => false, 'mensaje' => 'Tu renuncia ya está en proceso.'];

        return DB::transaction(function () use ($organizacionId, $usuarioId) {
            $solicitud = Solicitud::create(['tipo' => 'renuncia_rol', 'estado' => 0, 'solicitante_usuario_id' => $usuarioId, 'organizacion_id' => $organizacionId, 'datos_extra' => ['rol_renuncia_id' => 2], 'fecha_solicitud' => now()]);
            $admins = MiembroOrganizacion::where('organizacion_id', $organizacionId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->get();
            foreach ($admins as $admin) {
                Notificacion::create(['usuario_id' => $admin->usuario_id, 'solicitud_id' => $solicitud->id, 'titulo' => 'Renuncia a Supervisor', 'mensaje' => Auth::user()->nombres . ' desea dejar el cargo.', 'tipo' => 'solicitud_pendiente']);
            }
            $this->registrarHistorial($usuarioId, $organizacionId, 'solicitudes', $solicitud->id, 'SOLICITUD', 'Solicitud de renuncia a Supervisor');
            return ['success' => true, 'mensaje' => 'Solicitud enviada correctamente.'];
        });
    }

    public function aprobarIngresoMiembro(int $solicitudId)
    {
        return DB::transaction(function () use ($solicitudId) {
            $solicitud = Solicitud::with(['solicitante', 'organizacion'])->findOrFail($solicitudId);
            if ($solicitud->estado !== 0) return ['success' => false, 'mensaje' => 'Ya procesada.'];

            // Determinar quién es el usuario que se está uniendo o ascendiendo
            // Si es invitación: el destinatario es quien se une.
            // Si es petición (unirse/ascenso): el solicitante es quien se une.
            $targetUserId = ($solicitud->tipo === 'invitacion_organizacion')
                ? $solicitud->destinatario_usuario_id
                : $solicitud->solicitante_usuario_id;

            $miembro = MiembroOrganizacion::updateOrCreate(
                ['usuario_id' => $targetUserId, 'organizacion_id' => $solicitud->organizacion_id],
                ['es_propietario' => 0, 'estado' => 1, 'fecha_ingreso' => now(), 'deleted_at' => null]
            );

            if ($solicitud->tipo === 'unirse_organizacion' || $solicitud->tipo === 'invitacion_organizacion') {
                $rolId = $solicitud->datos_extra['rol_propuesto_id'] ?? 3;
                MiembroRol::updateOrCreate(['miembro_id' => $miembro->id, 'rol_id' => $rolId], ['estado' => 1]);
                $msg = 'Bienvenido a ' . $solicitud->organizacion->nombre;
            } elseif ($solicitud->tipo === 'ascenso_rol') {
                MiembroRol::updateOrCreate(['miembro_id' => $miembro->id, 'rol_id' => 2], ['estado' => 1]);
                $msg = 'Has sido ascendido a Supervisor.';
            } elseif ($solicitud->tipo === 'renuncia_rol') {
                MiembroRol::where('miembro_id', $miembro->id)->where('rol_id', 2)->update(['estado' => 0]);
                // Regla: Si deja de ser supervisor, se eliminan sus agricultores asignados
                AsignacionSupervisor::where('supervisor_miembro_id', $miembro->id)->where('organizacion_id', $solicitud->organizacion_id)->delete();
                $msg = 'Ya no eres Supervisor.';
            }

            $solicitud->update(['estado' => 1, 'fecha_respuesta' => now()]);

            // Notificar al usuario afectado
            Notificacion::create(['usuario_id' => $targetUserId, 'solicitud_id' => $solicitud->id, 'titulo' => 'Trámite Aceptado', 'mensaje' => $msg, 'tipo' => 'exito']);

            $this->registrarHistorial(Auth::id(), $solicitud->organizacion_id, 'miembros_organizacion', $solicitud->id, 'APROBACIÓN', 'Aprobación de ' . $solicitud->tipo);
            return ['success' => true, 'mensaje' => 'Trámite finalizado con éxito.'];
        });
    }

    public function abandonarOrganizacion(int $organizacionId)
    {
        $usuario = Auth::user();
        return DB::transaction(function () use ($usuario, $organizacionId) {
            $miembro = MiembroOrganizacion::where('usuario_id', $usuario->id)->where('organizacion_id', $organizacionId)->first();
            if (!$miembro) return ['success' => false, 'mensaje' => 'No perteneces a esta organización.'];

            if ($miembro->es_propietario) return ['success' => false, 'mensaje' => 'Como Propietario no puedes abandonar. Debes suspender la organización.'];

            // Regla: Si es Supervisor ACTIVO, debe renunciar primero
            $esSupervisorActivo = MiembroRol::where('miembro_id', $miembro->id)->where('rol_id', 2)->where('estado', 1)->exists();
            if ($esSupervisorActivo) {
                return ['success' => false, 'mensaje' => 'No puedes abandonar mientras seas Supervisor. Primero solicita tu renuncia al cargo y espera la aprobación.'];
            }

            $miembro->update(['estado' => 0]);
            MiembroRol::where('miembro_id', $miembro->id)->update(['estado' => 0]);

            $admins = MiembroOrganizacion::where('organizacion_id', $organizacionId)->whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->get();
            foreach ($admins as $admin) {
                Notificacion::create(['usuario_id' => $admin->usuario_id, 'titulo' => 'Salida de Miembro', 'mensaje' => $usuario->nombres . ' salió del equipo.', 'tipo' => 'error']);
            }
            $this->registrarHistorial($usuario->id, $organizacionId, 'miembros_organizacion', $miembro->id, 'BAJA', 'Salida voluntaria de la organización (Roles pausados).');
            return ['success' => true, 'mensaje' => 'Has salido de la organización.'];
        });
    }

    public function toggleSuspensionOrganizacion(int $organizacionId)
    {
        return DB::transaction(function () use ($organizacionId) {
            $org = Organizacion::findOrFail($organizacionId);
            $miembro = MiembroOrganizacion::where('usuario_id', Auth::id())->where('organizacion_id', $organizacionId)->where('es_propietario', 1)->first();
            if (!$miembro) return ['success' => false, 'mensaje' => 'No tienes permisos de propietario.'];
            $org->estado = !$org->estado;
            $org->save();
            $accionDesc = $org->estado ? 'REACTIVACIÓN' : 'SUSPENSIÓN';
            $this->registrarHistorial(Auth::id(), $organizacionId, 'organizaciones', $org->id, $accionDesc, 'El propietario cambió el estado global: ' . ($org->estado ? 'Activa' : 'Suspendida'));
            return ['success' => true, 'mensaje' => 'Estado de la organización actualizado.'];
        });
    }

    public function asignarAgricultorASupervisor(int $orgId, int $supervisorMiembroId, int $agricultorUsuarioId)
    {
        return DB::transaction(function () use ($orgId, $supervisorMiembroId, $agricultorUsuarioId) {
            // 1. Regla: Un agricultor solo puede tener un supervisor por organización
            $yaTieneSupervisor = AsignacionSupervisor::where('organizacion_id', $orgId)->where('agricultor_usuario_id', $agricultorUsuarioId)->exists();
            if ($yaTieneSupervisor) return ['success' => false, 'mensaje' => 'Este usuario ya tiene un supervisor asignado en esta organización.'];

            // 2. Regla: Un supervisor no puede ser supervisado
            $esSupervisor = MiembroOrganizacion::where('usuario_id', $agricultorUsuarioId)
                ->where('organizacion_id', $orgId)
                ->whereHas('roles', fn($q) => $q->where('rol_id', 2)->where('estado', 1))
                ->exists();

            if ($esSupervisor) return ['success' => false, 'mensaje' => 'No se puede asignar un supervisor a otro supervisor.'];

            $asignacion = AsignacionSupervisor::create(['organizacion_id' => $orgId, 'supervisor_miembro_id' => $supervisorMiembroId, 'agricultor_usuario_id' => $agricultorUsuarioId]);
            $supervisor = MiembroOrganizacion::with('usuario')->find($supervisorMiembroId);
            $organizacion = Organizacion::find($orgId);
            Notificacion::create(['usuario_id' => $agricultorUsuarioId, 'titulo' => 'Nuevo Supervisor Asignado', 'mensaje' => 'Se te ha asignado a ' . $supervisor->usuario->nombres . ' como supervisor.', 'tipo' => 'informativa']);
            $this->registrarHistorial(Auth::id(), $orgId, 'asignaciones_supervisor', $asignacion->id, 'ACTUALIZACIÓN', 'Asignación de supervisión.');
            return ['success' => true, 'mensaje' => 'Agricultor asignado correctamente.'];
        });
    }

    public function eliminarAsignacionSupervisor(int $asignacionId)
    {
        return DB::transaction(function () use ($asignacionId) {
            $asignacion = AsignacionSupervisor::findOrFail($asignacionId);
            $orgId = $asignacion->organizacion_id;
            $asignacion->delete();
            $this->registrarHistorial(Auth::id(), $orgId, 'asignaciones_supervisor', $asignacionId, 'BAJA', 'Se eliminó una asignación de supervisión.');
            return ['success' => true, 'mensaje' => 'Asignación eliminada.'];
        });
    }

    private function registrarHistorial($usuarioId, $orgId, $tabla, $regId, $accion, $desc, $previos = null)
    {
        HistorialProceso::create(['usuario_id' => $usuarioId, 'organizacion_id' => $orgId, 'tabla_afectada' => $tabla, 'registro_id' => $regId, 'accion' => $accion, 'descripcion' => $desc, 'detalles_previos' => $previos]);
    }
}
