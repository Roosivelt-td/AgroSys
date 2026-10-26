<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;
use App\Models\RolesOrganizacion;
use App\Models\Organizacion;
use App\Models\MiembroOrganizacion;
use App\Models\MiembroRol;
use App\Models\AsignacionSupervisor;
use Carbon\Carbon;

class UsuariosOrganizacionesSeeder extends Seeder
{
    /**
     * Seeder independiente para inserción de datos de usuarios y organizaciones.
     *
     * Genera aproximadamente 45 usuarios en total:
     *  - 5 Administradores que compraron el paquete de su organización (propietarios).
     *  - 10 Supervisores (2 supervisores por organización).
     *  - 25 Agricultores dentro de organización (5 por organización).
     *  - 5 Usuarios independientes (sin organización).
     *
     * Para ejecutar individualmente vía Docker Compose:
     * docker compose exec app php artisan db:seed --class=UsuariosOrganizacionesSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Garantizar Roles Globales
            $rolSuperAdmin = Rol::firstOrCreate(
                ['id' => 1],
                ['nombre' => 'Super Admin', 'descripcion' => 'Administrador global del sistema']
            );

            $rolAgricultorGlobal = Rol::firstOrCreate(
                ['id' => 2],
                ['nombre' => 'Agricultor', 'descripcion' => 'Usuario estándar (Agricultor)']
            );

            // 2. Garantizar Roles de Organización (Cargos Internos)
            $rolOrgAdmin = RolesOrganizacion::firstOrCreate(
                ['nombre' => 'Administrador'],
                ['descripcion' => 'Responsable total de la gestión de la organización, miembros y terrenos.']
            );

            $rolOrgSupervisor = RolesOrganizacion::firstOrCreate(
                ['nombre' => 'Supervisor'],
                ['descripcion' => 'Encargado de monitorear y asignar tareas a los agricultores bajo su cargo.']
            );

            $rolOrgAgricultor = RolesOrganizacion::firstOrCreate(
                ['nombre' => 'Agricultor'],
                ['descripcion' => 'Personal operativo encargado de los cultivos y labores en campo.']
            );

            $passwordComun = Hash::make('password123');

            // Datos de las 5 Organizaciones con sus Administradores (Compradores del plan)
            $empresasData = [
                [
                    'nombre' => 'Agropecuaria Valle Verde S.A.C.',
                    'ruc' => '20601234561',
                    'telefono' => '+51 987654321',
                    'email' => 'contacto@valleverde.pe',
                    'direccion' => 'Av. Los Panaderos 123, Ica',
                    'monto' => 1500.00,
                    'admin_nombre' => 'Carlos',
                    'admin_apellido' => 'Mendoza Valles',
                    'admin_email' => 'admin.valleverde@agrosys.com',
                    'admin_dni' => '45123890',
                ],
                [
                    'nombre' => 'Cooperativa Agrícola San Isidro',
                    'ruc' => '20601234562',
                    'telefono' => '+51 987654322',
                    'email' => 'info@sanisidro.org.pe',
                    'direccion' => 'Valle de Cañete Km 14, Lima',
                    'monto' => 1800.00,
                    'admin_nombre' => 'María',
                    'admin_apellido' => 'Sánchez Paredes',
                    'admin_email' => 'admin.sanisidro@agrosys.com',
                    'admin_dni' => '45123891',
                ],
                [
                    'nombre' => 'Fundo El Olivar de Chincha',
                    'ruc' => '20601234563',
                    'telefono' => '+51 987654323',
                    'email' => 'administracion@elolivar.com',
                    'direccion' => 'Fundo El Olivar Lote 4, Chincha',
                    'monto' => 1200.00,
                    'admin_nombre' => 'Roberto',
                    'admin_apellido' => 'Gómez Farfán',
                    'admin_email' => 'admin.elolivar@agrosys.com',
                    'admin_dni' => '45123892',
                ],
                [
                    'nombre' => 'Agroexportadora Sol del Norte',
                    'ruc' => '20601234564',
                    'telefono' => '+51 987654324',
                    'email' => 'ventas@soldelnorte.pe',
                    'direccion' => 'Carretera Panamericana Norte Km 520, Piura',
                    'monto' => 2500.00,
                    'admin_nombre' => 'Lucía',
                    'admin_apellido' => 'Torres Castro',
                    'admin_email' => 'admin.soldelnorte@agrosys.com',
                    'admin_dni' => '45123893',
                ],
                [
                    'nombre' => 'Campos del Sur EIRL',
                    'ruc' => '20601234565',
                    'telefono' => '+51 987654325',
                    'email' => 'contacto@camposdelsur.pe',
                    'direccion' => 'Sector Majes Parcela 88, Arequipa',
                    'monto' => 1500.00,
                    'admin_nombre' => 'Fernando',
                    'admin_apellido' => 'Vargas Ríos',
                    'admin_email' => 'admin.camposdelsur@agrosys.com',
                    'admin_dni' => '45123894',
                ],
            ];

            // Listado de Supervisores (10 en total, 2 por org)
            $nombresSupervisores = [
                ['Juan', 'Pérez Quispe'], ['Jorge', 'López Ugarte'],
                ['Ana', 'Ramírez Flores'], ['Luis', 'Fernández Vega'],
                ['Sonia', 'Espinoza Díaz'], ['César', 'Morales Benítez'],
                ['Patricia', 'García Medina'], ['Héctor', 'Ruiz Huamán'],
                ['Elena', 'Castro Delgado'], ['Óscar', 'Guerrero Silva'],
            ];

            // Listado de Agricultores vinculados a Organización (25 en total, 5 por org)
            $nombresAgricultores = [
                ['Pedro', 'Castillo Azaña'], ['Rosa', 'Flores Yupanqui'],
                ['Guillermo', 'Solís Vera'], ['Diana', 'Córdova Pineda'],
                ['Raúl', 'Alvarado Neyra'], ['Carmen', 'Rojas Palomino'],
                ['Gonzalo', 'Ibáñez Tello'], ['Verónica', 'Villanueva Ríos'],
                ['Manuel', 'Campos Chira'], ['Silvia', 'Soto Meléndez'],
                ['Javier', 'Salazar Cruz'], ['Nuria', 'Aquino Romero'],
                ['Felipe', 'Mamani Condori'], ['Beatriz', 'Estrada Cisneros'],
                ['Hugo', 'Guzmán Ponce'], ['Mónica', 'Valdez Ramos'],
                ['Efraín', 'Arias Zeballos'], ['Yolanda', 'Paredes Cárdenas'],
                ['Gabriel', 'Núñez Loayza'], ['Irene', 'Carrillo Ocas'],
                ['Arturo', 'Paredes Meza'], ['Sofía', 'Navarro Quintana'],
                ['Dante', 'Barrientos Gil'], ['Teresa', 'Aponte Solano'],
                ['Renzo', 'Dávila Paredes'],
            ];

            // Listado de Usuarios Independientes (Sin Organización)
            $nombresIndependientes = [
                ['Hernán', 'Chávez Barreto'], ['Gloria', 'Sarmiento León'],
                ['Walter', 'Linares Peña'], ['Claudia', 'Mendoza Prado'],
                ['Mariano', 'Castañeda Rivas'],
            ];

            $supervisorIndex = 0;
            $agricultorIndex = 0;

            // 3. Crear las 5 organizaciones y sus miembros
            foreach ($empresasData as $i => $empData) {
                $orgNum = $i + 1;

                // Crear Organización con paquete/suscripción
                $org = Organizacion::create([
                    'nombre' => $empData['nombre'],
                    'descripcion' => 'Empresa agrícola con suscripción activa en el sistema AgroSys.',
                    'ruc' => $empData['ruc'],
                    'telefono' => $empData['telefono'],
                    'email' => $empData['email'],
                    'direccion' => $empData['direccion'],
                    'estado' => 1,
                    'fecha_adquisicion' => Carbon::now()->subMonths(rand(1, 6)),
                    'fecha_vencimiento' => Carbon::now()->addMonths(rand(6, 12)),
                    'anios_suscripcion' => 1,
                    'monto_pago' => $empData['monto'],
                ]);

                // 3a. Usuario Administrador (Comprador del paquete de administración)
                $adminUser = User::create([
                    'rol_id' => $rolAgricultorGlobal->id,
                    'nombres' => $empData['admin_nombre'],
                    'apellidos' => $empData['admin_apellido'],
                    'email' => $empData['admin_email'],
                    'password' => $passwordComun,
                    'telefono' => $empData['telefono'],
                    'dni' => $empData['admin_dni'],
                    'estado' => 1,
                    'experiencia_anios' => rand(6, 18),
                    'nivel_educativo' => 'Universitario',
                    'ubicacion' => $empData['direccion'],
                    'descripcion' => 'Administrador y propietario de la organización.',
                    'is_activo' => true,
                ]);

                // Registrar Membresía como Propietario (es_propietario = 1)
                $miembroAdmin = MiembroOrganizacion::create([
                    'usuario_id' => $adminUser->id,
                    'organizacion_id' => $org->id,
                    'es_propietario' => 1,
                    'estado' => 1,
                    'fecha_ingreso' => $org->fecha_adquisicion,
                ]);

                // Asignar Cargo de Administrador
                MiembroRol::create([
                    'miembro_id' => $miembroAdmin->id,
                    'rol_id' => $rolOrgAdmin->id,
                    'estado' => 1,
                ]);

                // 3b. Crear 2 Supervisores para la Organización
                $miembrosSupervisores = [];
                for ($s = 0; $s < 2; $s++) {
                    $supNombre = $nombresSupervisores[$supervisorIndex];
                    $supervisorIndex++;
                    $supNum = $s + 1;

                    $supUser = User::create([
                        'rol_id' => $rolAgricultorGlobal->id,
                        'nombres' => $supNombre[0],
                        'apellidos' => $supNombre[1],
                        'email' => "supervisor{$orgNum}_{$supNum}@agrosys.com",
                        'password' => $passwordComun,
                        'telefono' => '+51 9' . (55000000 + ($orgNum * 10) + $supNum),
                        'dni' => (string)(55123800 + ($orgNum * 10) + $supNum),
                        'estado' => 1,
                        'experiencia_anios' => rand(3, 10),
                        'nivel_educativo' => 'Técnico',
                        'ubicacion' => $empData['direccion'],
                        'descripcion' => "Supervisor de campo en {$org->nombre}",
                        'is_activo' => true,
                    ]);

                    $miembroSup = MiembroOrganizacion::create([
                        'usuario_id' => $supUser->id,
                        'organizacion_id' => $org->id,
                        'es_propietario' => 0,
                        'estado' => 1,
                        'fecha_ingreso' => Carbon::now()->subMonths(rand(1, 5)),
                    ]);

                    MiembroRol::create([
                        'miembro_id' => $miembroSup->id,
                        'rol_id' => $rolOrgSupervisor->id,
                        'estado' => 1,
                    ]);

                    $miembrosSupervisores[] = $miembroSup;
                }

                // 3c. Crear 5 Agricultores en la Organización
                for ($a = 0; $a < 5; $a++) {
                    $agrNombre = $nombresAgricultores[$agricultorIndex];
                    $agricultorIndex++;
                    $agrNum = $a + 1;

                    $agrUser = User::create([
                        'rol_id' => $rolAgricultorGlobal->id,
                        'nombres' => $agrNombre[0],
                        'apellidos' => $agrNombre[1],
                        'email' => "agricultor{$orgNum}_{$agrNum}@agrosys.com",
                        'password' => $passwordComun,
                        'telefono' => '+51 9' . (65000000 + ($orgNum * 10) + $agrNum),
                        'dni' => (string)(65123800 + ($orgNum * 10) + $agrNum),
                        'estado' => 1,
                        'experiencia_anios' => rand(1, 8),
                        'nivel_educativo' => 'Secundaria',
                        'ubicacion' => $empData['direccion'],
                        'descripcion' => "Agricultor operativo en {$org->nombre}",
                        'is_activo' => true,
                    ]);

                    $miembroAgr = MiembroOrganizacion::create([
                        'usuario_id' => $agrUser->id,
                        'organizacion_id' => $org->id,
                        'es_propietario' => 0,
                        'estado' => 1,
                        'fecha_ingreso' => Carbon::now()->subMonths(rand(1, 4)),
                    ]);

                    MiembroRol::create([
                        'miembro_id' => $miembroAgr->id,
                        'rol_id' => $rolOrgAgricultor->id,
                        'estado' => 1,
                    ]);

                    // Asignar este agricultor a uno de los supervisores de la organización
                    $supervisorElegido = $miembrosSupervisores[$a % 2];
                    AsignacionSupervisor::create([
                        'organizacion_id' => $org->id,
                        'supervisor_miembro_id' => $supervisorElegido->id,
                        'agricultor_usuario_id' => $agrUser->id,
                    ]);
                }
            }

            // 4. Crear 5 Usuarios Independientes (Sin Organización)
            foreach ($nombresIndependientes as $idx => $indNombre) {
                $indNum = $idx + 1;
                User::create([
                    'rol_id' => $rolAgricultorGlobal->id,
                    'nombres' => $indNombre[0],
                    'apellidos' => $indNombre[1],
                    'email' => "independiente{$indNum}@agrosys.com",
                    'password' => $passwordComun,
                    'telefono' => '+51 9' . (75000000 + $indNum),
                    'dni' => (string)(75123800 + $indNum),
                    'estado' => 1,
                    'experiencia_anios' => rand(1, 10),
                    'nivel_educativo' => 'Secundaria',
                    'ubicacion' => 'Zona Rural Independiente',
                    'descripcion' => 'Agricultor independiente (no pertenece a ninguna organización).',
                    'is_activo' => true,
                ]);
            }

            DB::commit();

            if (isset($this->command)) {
                $this->command->info('====================================================');
                $this->command->info(' ¡UsuariosOrganizacionesSeeder ejecutado con éxito! ');
                $this->command->info('====================================================');
                $this->command->info('Resumen de datos creados:');
                $this->command->info(' - 5 Organizaciones creadas con suscripción activa.');
                $this->command->info(' - 5 Administradores de Organización (Compradores del paquete).');
                $this->command->info(' - 10 Supervisores de Organización (2 por organización).');
                $this->command->info(' - 25 Agricultores miembros de Organización (5 por organización).');
                $this->command->info(' - 5 Usuarios Independientes (sin organización).');
                $this->command->info(' - Asignaciones de supervisión registradas.');
                $this->command->info('----------------------------------------------------');
                $this->command->info('Contraseña por defecto para todos los usuarios: password123');
                $this->command->info('Ejemplo email Administrador: admin.valleverde@agrosys.com');
                $this->command->info('Ejemplo email Supervisor: supervisor1_1@agrosys.com');
                $this->command->info('Ejemplo email Agricultor: agricultor1_1@agrosys.com');
                $this->command->info('Ejemplo email Independiente: independiente1@agrosys.com');
                $this->command->info('====================================================');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($this->command)) {
                $this->command->error('Error durante la ejecución del Seeder: ' . $e->getMessage());
            }
            throw $e;
        }
    }
}
