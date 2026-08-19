<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\CatalogoCultivo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insertar Catálogo de Cultivos
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

        // 2. Crear 5 Agricultores
        $nombres = ['Juan', 'Pedro', 'Maria', 'Luisa', 'Carlos'];
        $apellidos = ['Quispe', 'Gomez', 'Sosa', 'Rojas', 'Mendoza'];

        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'rol_id' => 2, // Agricultor
                'nombres' => $nombres[$i],
                'apellidos' => $apellidos[$i],
                'email' => strtolower($nombres[$i]) . "@agrosys.com",
                'password' => Hash::make('password'),
                'dni' => '1234567' . $i,
                'estado' => 1,
                'is_activo' => 1,
            ]);

            // 3. Crear 8 Terrenos para cada Agricultor
            for ($j = 1; $j <= 8; $j++) {
                $terreno = Terreno::create([
                    'usuario_id' => $user->id,
                    'nombre' => "Fundo " . $user->nombres . " " . $j,
                    'ubicacion' => "Sector Agrícola " . chr(64 + $j) . ", Ayacucho",
                    'latitud' => -13.15 + (rand(-100, 100) / 1000),
                    'longitud' => -74.22 + (rand(-100, 100) / 1000),
                    'hectareas' => rand(2, 15),
                    'tipo_tenencia' => rand(0, 1) ? 'propio' : 'alquilado',
                    'costo_alquiler_anual' => rand(0, 1) ? 0 : rand(500, 2000),
                    'calidad_suelo' => ['arcilloso', 'franco', 'arenoso', 'limoso'][rand(0, 3)],
                    'fuente_agua' => ['Riego por goteo', 'Pozo', 'Canal', 'Lluvia'][rand(0, 3)],
                    'estado_terreno' => 'activo',
                    'estado' => 1,
                ]);

                // 4. Crear Cultivos (aprox 40 en total repartidos, aquí pondremos 1 por terreno para asegurar 40)
                $cat = $catalogosDb->random();
                $estado = ['Planificado', 'En crecimiento', 'Cosechado', 'Perdido'][rand(0, 3)];
                $fechaSiembra = Carbon::now()->subDays(rand(0, 150));

                Cultivo::create([
                    'terreno_id' => $terreno->id,
                    'catalogo_cultivo_id' => $cat->id,
                    'nombre_lote' => "LOTE-" . $j . "-" . strtoupper($cat->nombre),
                    'variedad' => 'Genérica',
                    'fecha_planificada' => $fechaSiembra->copy()->subDays(15),
                    'fecha_siembra' => $fechaSiembra,
                    'fecha_cosecha_estimada' => $fechaSiembra->copy()->addDays($cat->dias_a_cosecha_promedio),
                    'fecha_cosecha_finalizada' => ($estado === 'Cosechado') ? Carbon::now() : null,
                    'estado' => $estado,
                    'area_destinada' => $terreno->hectareas * (rand(50, 100) / 100),
                    'plantas_estimadas' => rand(500, 5000),
                    'rendimiento_esperado_tn_ha' => rand(5, 40),
                ]);
            }
        }
    }
}
