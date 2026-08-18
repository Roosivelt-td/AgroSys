<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\MiembroRol;
use App\Models\RolesOrganizacion;
use App\Models\Solicitud;
use App\Models\Notificacion;
use App\Models\HistorialProceso;
use App\Models\AsignacionSupervisor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComplexSystemSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('miembro_roles')->truncate();
        DB::table('miembros_organizacion')->truncate();
        DB::table('organizaciones')->truncate();
        DB::table('solicitudes')->truncate();
        DB::table('notificaciones')->truncate();
        DB::table('historial_procesos')->truncate();
        DB::table('asignaciones_supervisor')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create('es_PE');
        $ahora = Carbon::now();
        $haceUnMes = Carbon::now()->subMonth();

        // 1. Crear Organizaciones Base (15 orgs)
        $organizaciones = [];
        for ($i = 0; $i < 15; $i++) {
            $fechaOrg = $haceUnMes->copy()->addDays(rand(1, 5));
            $organizaciones[] = Organizacion::create([
                'nombre' => 'Agrícola ' . $faker->company,
                'ruc' => '20' . $faker->randomNumber(9, true),
                'descripcion' => $faker->sentence,
                'email' => $faker->companyEmail,
                'estado' => 1,
                'created_at' => $fechaOrg
            ]);
        }

        // 2. Crear Usuarios (65 en total)
        // 10 Dueños / Admins complejos
        // 50 Agricultores base
        // 5 Agricultores comunes (sin org)
        $usuarios = [];
        for ($i = 1; $i <= 65; $i++) {
            $fechaReg = $haceUnMes->copy()->addDays(rand(0, 10));
            $u = User::create([
                'rol_id' => 2, // Agricultor global
                'nombres' => $faker->firstName,
                'apellidos' => $faker->lastName . ' ' . $faker->lastName,
                'email' => "user{$i}@agrosys.com",
                'dni' => $faker->randomNumber(8, true),
                'password' => Hash::make('password'),
                'ubicacion' => 'Ayacucho, ' . $faker->city,
                'experiencia_anios' => rand(1, 20),
                'created_at' => $fechaReg
            ]);

            // Historial de Registro
            HistorialProceso::create([
                'usuario_id' => $u->id,
                'tabla_afectada' => 'usuarios',
                'registro_id' => $u->id,
                'accion' => 'REGISTRO',
                'descripcion' => 'TE UNISTE AL SISTEMA',
                'created_at' => $fechaReg
            ]);
            $usuarios[] = $u;
        }

        // 3. Lógica de Dueños (Primeros 10 usuarios)
        // Pertenecen a 1-3 orgs, son Admins y algunos Supervisores
        for ($i = 0; $i < 10; $i++) {
            $u = $usuarios[$i];
            $numOrgs = rand(1, 3);
            $misOrgs = collect($organizaciones)->random($numOrgs);

            foreach ($misOrgs as $org) {
                $fechaJoin = $u->created_at->addDays(2);

                // Membresía
                $m = MiembroOrganizacion::create([
                    'usuario_id' => $u->id,
                    'organizacion_id' => $org->id,
                    'es_propietario' => 1,
                    'estado' => 1,
                    'fecha_ingreso' => $fechaJoin,
                    'created_at' => $fechaJoin
                ]);

                // Roles (Admin y Agricultor obligatorio)
                MiembroRol::create(['miembro_id' => $m->id, 'rol_id' => 1, 'estado' => 1]);
                MiembroRol::create(['miembro_id' => $m->id, 'rol_id' => 3, 'estado' => 1]);

                // ¿Es Supervisor también? (50% probabilidad)
                if (rand(0, 1)) {
                    MiembroRol::create(['miembro_id' => $m->id, 'rol_id' => 2, 'estado' => 1]);
                }

                HistorialProceso::create([
                    'usuario_id' => $u->id,
                    'organizacion_id' => $org->id,
                    'tabla_afectada' => 'organizaciones',
                    'registro_id' => $org->id,
                    'accion' => 'APROBACIÓN',
                    'descripcion' => 'Se aprobó la creación y asignación de cargos para ' . $org->nombre,
                    'created_at' => $fechaJoin
                ]);
            }
        }

        // 4. Lógica de Agricultores (Siguientes 50 usuarios)
        // Asignarlos a organizaciones y algunos hacerlos Supervisores (2-3 por org)
        $agris = array_slice($usuarios, 10, 50);
        foreach ($agris as $u) {
            $org = collect($organizaciones)->random();
            $fechaSolicitud = $u->created_at->addDays(1);

            // Crear proceso de solicitud
            $s = Solicitud::create([
                'tipo' => 'unirse_organizacion',
                'estado' => 1, // Aceptada
                'solicitante_usuario_id' => $u->id,
                'organizacion_id' => $org->id,
                'fecha_solicitud' => $fechaSolicitud,
                'fecha_respuesta' => $fechaSolicitud->copy()->addHours(5),
                'created_at' => $fechaSolicitud
            ]);

            $m = MiembroOrganizacion::create([
                'usuario_id' => $u->id,
                'organizacion_id' => $org->id,
                'estado' => 1,
                'fecha_ingreso' => $s->fecha_respuesta,
                'created_at' => $s->fecha_respuesta
            ]);

            MiembroRol::create(['miembro_id' => $m->id, 'rol_id' => 3, 'estado' => 1]);

            HistorialProceso::create([
                'usuario_id' => $u->id,
                'organizacion_id' => $org->id,
                'tabla_afectada' => 'solicitudes',
                'registro_id' => $s->id,
                'accion' => 'SOLICITUD',
                'descripcion' => 'ENVIÓ UNA PETICIÓN DE INGRESO',
                'created_at' => $fechaSolicitud
            ]);
        }

        // 5. Asignar Supervisores (2-3 por org) y sus agricultores (5-10 cada uno)
        foreach ($organizaciones as $org) {
            $miembrosOrg = MiembroOrganizacion::where('organizacion_id', $org->id)->get();
            $posiblesSupervisores = $miembrosOrg->take(3); // Tomamos 3 para ser supervisores

            foreach ($posiblesSupervisores as $supM) {
                // Darle rol de supervisor si no lo tiene
                MiembroRol::firstOrCreate(['miembro_id' => $supM->id, 'rol_id' => 2], ['estado' => 1]);

                // Asignarle entre 5 y 10 agricultores de la misma org
                $otrosAgris = $miembrosOrg->where('id', '!=', $supM->id)->random(min($miembrosOrg->count() - 1, rand(5, 10)));

                foreach ($otrosAgris as $agriM) {
                    AsignacionSupervisor::create([
                        'organizacion_id' => $org->id,
                        'supervisor_miembro_id' => $supM->id,
                        'agricultor_usuario_id' => $agriM->usuario_id,
                        'created_at' => $supM->created_at->addDays(1)
                    ]);
                }
            }
        }

        // 6. Generar Solicitudes Rechazadas y Notificaciones (Interacción)
        for ($i = 0; $i < 20; $i++) {
            $u = collect($usuarios)->random();
            $org = collect($organizaciones)->random();

            $s = Solicitud::create([
                'tipo' => 'unirse_organizacion',
                'estado' => 2, // Rechazada
                'solicitante_usuario_id' => $u->id,
                'organizacion_id' => $org->id,
                'fecha_solicitud' => $ahora->copy()->subDays(rand(1, 15)),
                'fecha_respuesta' => $ahora->copy()->subDays(rand(0, 1))
            ]);

            Notificacion::create([
                'usuario_id' => $u->id,
                'solicitud_id' => $s->id,
                'titulo' => 'Solicitud Rechazada',
                'mensaje' => 'Tu petición para unirte a ' . $org->nombre . ' fue denegada.',
                'tipo' => 'error',
                'created_at' => $s->fecha_respuesta
            ]);
        }

        // 7. Generar Invitaciones de Supervisor para probar el Modal de Perfil
        $admins = MiembroOrganizacion::whereHas('roles.rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->get()->take(5);
        foreach ($admins as $admin) {
            $agriLibre = User::where('rol_id', 2)
                ->whereNotIn('id', MiembroOrganizacion::where('organizacion_id', $admin->organizacion_id)->pluck('usuario_id'))
                ->first();

            if ($agriLibre) {
                $s = Solicitud::create([
                    'tipo' => 'invitacion_organizacion',
                    'estado' => 0,
                    'solicitante_usuario_id' => $admin->usuario_id,
                    'destinatario_usuario_id' => $agriLibre->id,
                    'organizacion_id' => $admin->organizacion_id,
                    'datos_extra' => ['rol_propuesto_id' => 2, 'rol_nombre' => 'Supervisor'],
                    'fecha_solicitud' => $ahora->copy()->subDays(1)
                ]);

                Notificacion::create([
                    'usuario_id' => $agriLibre->id,
                    'solicitud_id' => $s->id,
                    'titulo' => 'Invitación de Cargo',
                    'mensaje' => 'Has sido invitado a ser Supervisor en ' . $admin->organizacion->nombre,
                    'tipo' => 'solicitud_pendiente',
                    'created_at' => $s->fecha_solicitud
                ]);
            }
        }
    }
}
