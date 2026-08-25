<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\CatalogoCultivo;
use Carbon\Carbon;

class ComplexSystemSeeder extends Seeder
{
    /**
     * Seed inicial del sistema AgroSys.
     * Registra roles globales, roles de organizacion, catalogos base y Super Admin.
     */
    public function run(): void
    {
        // 1. Registro de Roles Globales
        $rolesGlobales = [
            ['id' => 1, 'nombre' => 'Super Admin', 'descripcion' => 'Administrador global del sistema', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Agricultor', 'descripcion' => 'Usuario estándar (Agricultor)', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('rol')->insert($rolesGlobales);

        // 2. Registro de Roles de Organización (Cargos Internos)
        $rolesOrg = [
            ['id' => 1, 'nombre' => 'Administrador', 'descripcion' => 'Responsable total de la gestión de la organización, miembros y terrenos.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Supervisor', 'descripcion' => 'Encargado de monitorear y asignar tareas a los agricultores bajo su cargo.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Agricultor', 'descripcion' => 'Personal operativo encargado de los cultivos y labores en campo.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Agricultor_Grado_1', 'descripcion' => 'Agricultor con experiencia básica o en entrenamiento.', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles_organizacion')->insert($rolesOrg);

        // 3. Registro del Usuario Super Admin inicial
        DB::table('usuarios')->insert([
            'rol_id' => 1,
            'nombres' => 'Super',
            'apellidos' => 'Administrador',
            'email' => 'admin@agrosys.com',
            'password' => Hash::make('password'),
            'dni' => '00000000',
            'estado' => 1,
            'is_activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Catálogo de Labores Base
        $labores = [
            ['nombre' => 'Preparar', 'categoria' => 'preparacion', 'descripcion' => 'Preparación física del suelo para la siembra.'],
            ['nombre' => 'Siembra', 'categoria' => 'siembra', 'descripcion' => 'Colocación de semillas o plantones en el terreno.'],
            ['nombre' => 'Riego', 'categoria' => 'mantenimiento', 'descripcion' => 'Suministro de agua a los cultivos.'],
            ['nombre' => 'Fumigar', 'categoria' => 'mantenimiento', 'descripcion' => 'Aplicación de productos fitosanitarios para control de plagas.'],
            ['nombre' => 'Aporque', 'categoria' => 'mantenimiento', 'descripcion' => 'Acumulación de tierra en la base de las plantas.'],
            ['nombre' => 'Deshierbe', 'categoria' => 'mantenimiento', 'descripcion' => 'Eliminación manual o química de malezas.'],
            ['nombre' => 'Abonar', 'categoria' => 'mantenimiento', 'descripcion' => 'Aplicación de fertilizantes para nutrición vegetal.'],
            ['nombre' => 'Cosechar', 'categoria' => 'cosecha', 'descripcion' => 'Recolección de los productos agrícolas.'],
            ['nombre' => 'Otros', 'categoria' => 'mantenimiento', 'descripcion' => 'Actividades diversas no clasificadas.'],
        ];

        foreach ($labores as $labor) {
            DB::table('catalogo_labores')->insert(array_merge($labor, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        // 5. Insertar Catálogo de Cultivos
                $catalogos = [
                    ['nombre' => 'Maiz', 'nombre_cientifico' => 'Zea mays', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 6, 'dias_a_cosecha_promedio' => 120, 'instrucciones_base_riego' => 'Riego regular manteniendo humedad adecuada del suelo.', 'instrucciones_base_plagas' => 'Monitorear gusano cogollero, gusano de tierra y pulgones.'],
                    ['nombre' => 'Papa', 'nombre_cientifico' => 'Solanum tuberosum', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 5, 'dias_a_cosecha_promedio' => 120, 'instrucciones_base_riego' => 'Mantener humedad constante durante el crecimiento de los tuberculos.', 'instrucciones_base_plagas' => 'Vigilar polilla de la papa, pulgones y gusano de tierra.'],
                    ['nombre' => 'Palta', 'nombre_cientifico' => 'Persea americana', 'tipo_ciclo' => 'perenne', 'vida_util_estimada_meses' => 240, 'dias_a_cosecha_promedio' => 1095, 'instrucciones_base_riego' => 'Realizar riegos frecuentes y controlados evitando encharcamientos.', 'instrucciones_base_plagas' => 'Monitorear trips, arañita roja, mosca blanca y barrenadores.'],
                    ['nombre' => 'Mango', 'nombre_cientifico' => 'Mangifera indica', 'tipo_ciclo' => 'perenne', 'vida_util_estimada_meses' => 300, 'dias_a_cosecha_promedio' => 1460, 'instrucciones_base_riego' => 'Regar regularmente durante el crecimiento y desarrollo del fruto.', 'instrucciones_base_plagas' => 'Vigilar mosca de la fruta, trips, cochinillas y acaros.'],
                    ['nombre' => 'Arroz', 'nombre_cientifico' => 'Oryza sativa', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 5, 'dias_a_cosecha_promedio' => 130, 'instrucciones_base_riego' => 'Mantener una lamina de agua controlada durante las etapas necesarias.', 'instrucciones_base_plagas' => 'Monitorear sogata, chinches y gusanos defoliadores.'],
                    ['nombre' => 'Cebolla', 'nombre_cientifico' => 'Allium cepa', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 6, 'dias_a_cosecha_promedio' => 150, 'instrucciones_base_riego' => 'Aplicar riegos ligeros y frecuentes durante el crecimiento del bulbo.', 'instrucciones_base_plagas' => 'Vigilar trips, mosca de la cebolla y enfermedades foliares.'],
                    ['nombre' => 'Tomate', 'nombre_cientifico' => 'Solanum lycopersicum', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 6, 'dias_a_cosecha_promedio' => 90, 'instrucciones_base_riego' => 'Mantener humedad uniforme mediante riegos regulares.', 'instrucciones_base_plagas' => 'Monitorear mosca blanca, trips, pulgones y minadores.'],
                    ['nombre' => 'Aji', 'nombre_cientifico' => 'Capsicum annuum', 'tipo_ciclo' => 'ciclo_corto', 'vida_util_estimada_meses' => 8, 'dias_a_cosecha_promedio' => 120, 'instrucciones_base_riego' => 'Realizar riegos frecuentes y moderados durante floracion y formacion de frutos.', 'instrucciones_base_plagas' => 'Controlar pulgones, trips, mosca blanca y acaros.'],
                    ['nombre' => 'Uva', 'nombre_cientifico' => 'Vitis vinifera', 'tipo_ciclo' => 'perenne', 'vida_util_estimada_meses' => 240, 'dias_a_cosecha_promedio' => 730, 'instrucciones_base_riego' => 'Aplicar riego controlado durante el crecimiento y desarrollo de frutos.', 'instrucciones_base_plagas' => 'Monitorear cochinillas, trips, acaros y mosca de la fruta.'],
                    ['nombre' => 'Arandano', 'nombre_cientifico' => 'Vaccinium corymbosum', 'tipo_ciclo' => 'perenne', 'vida_util_estimada_meses' => 120, 'dias_a_cosecha_promedio' => 730, 'instrucciones_base_riego' => 'Mantener humedad constante sin saturar el suelo. Preferentemente usar riego por goteo.', 'instrucciones_base_plagas' => 'Vigilar trips, pulgones, acaros y mosca de la fruta.'],
                ];

                foreach ($catalogos as $cat) {
                    CatalogoCultivo::updateOrCreate(
                        ['nombre' => $cat['nombre']],
                        array_merge($cat, [
                            'foto_path' => null,
                            'es_personalizado' => 0,
                            'usuario_creador_id' => 1,
                        ])
                    );
                }

                $catalogosDb = CatalogoCultivo::all();

        // 6. Configuración inicial del sistema
        DB::table('configuracion_sistema')->insert([
            'nombre_sistema' => 'AgroSys',
            'lema_sistema' => 'Gestión Agrícola Digital Inteligente',
            'version_sistema' => '1.0.0',
            'color_primario' => '#2D6A4F',
            'color_secundario' => '#40916C',
            'updated_by_usuario_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Tipos de Mano de Obra base
        DB::table('mano_obra_tipo')->insert([
            ['nombre' => 'Jornalero', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Operador de Maquinaria', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Especialista Técnico', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
