<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Terreno;
use App\Models\User;
use App\Models\MiembroOrganizacion;
use Carbon\Carbon;

class TerrenosAyacuchoSeeder extends Seeder
{
    /**
     * Seeder independiente para registrar ÚNICAMENTE terrenos físicos en Ayacucho, Perú.
     *
     * Registra específicamente 5 terrenos reales en Ayacucho para el usuario
     * `agricultor4_2@agrosys.com` y asigna a cada uno de los demás usuarios sus terrenos
     * correspondientes con `usuario_id = $user->id`, garantizando que en su Dashboard
     * y en su vista de "Ver Terrenos" figure exactamente la misma información.
     *
     * Para ejecutar individualmente vía Docker Compose:
     * docker compose exec app php artisan db:seed --class=TerrenosAyacuchoSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Limpiar datos residuales (cultivos, labores, cosechas, ventas y terrenos)
            // para garantizar que la base de datos solo contenga registros puros de terrenos
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('ventas')->truncate();
            DB::table('cosechas')->truncate();
            DB::table('labores')->truncate();
            DB::table('cultivos')->truncate();
            DB::table('clima_registros')->truncate();
            DB::table('terrenos')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $usuarios = User::all();

            // Si no existen usuarios, ejecutar UsuariosOrganizacionesSeeder primero
            if ($usuarios->isEmpty()) {
                if (isset($this->command)) {
                    $this->command->warn('No se encontraron usuarios. Ejecutando UsuariosOrganizacionesSeeder primero...');
                }
                $this->call(UsuariosOrganizacionesSeeder::class);
                $usuarios = User::all();
            }

            // Datos geográficos de la Región Ayacucho, Perú
            $ubicacionesAyacucho = [
                [
                    'distrito' => 'Quinua, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Quinua',
                    'referencia' => 'Km 15 Carretera a Quinua, cerca al Obelisco de la Pampa de Ayacucho',
                    'lat' => -13.058333,
                    'lng' => -74.138056,
                    'fuentes' => ['Canal de Riego Quinua', 'Manantial Chacco'],
                ],
                [
                    'distrito' => 'Luricocha, Huanta',
                    'ubicacion_text' => 'Ayacucho - Huanta - Luricocha',
                    'referencia' => 'Valle Frutícola de Luricocha, Sector Azángaro Lote 8',
                    'lat' => -12.936111,
                    'lng' => -74.250000,
                    'fuentes' => ['Río Cachi', 'Riego por Goteo'],
                ],
                [
                    'distrito' => 'Socos, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Socos',
                    'referencia' => 'Comunidad Campesina de Socos, Sector Los Olivos S/N',
                    'lat' => -13.216667,
                    'lng' => -74.316667,
                    'fuentes' => ['Pozo Subterráneo', 'Canal de Riego Cachi'],
                ],
                [
                    'distrito' => 'Pacaycasa, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Pacaycasa',
                    'referencia' => 'Carretera Central Ayacucho-Huanta Km 12, Pampa de Pacaycasa',
                    'lat' => -13.052200,
                    'lng' => -74.223600,
                    'fuentes' => ['Río Yucay', 'Canal Principal'],
                ],
                [
                    'distrito' => 'San Miguel, La Mar',
                    'ubicacion_text' => 'Ayacucho - La Mar - San Miguel',
                    'referencia' => 'Valle del Río Torobamba, Sector Ninabamba Parcela 4',
                    'lat' => -13.013889,
                    'lng' => -73.981111,
                    'fuentes' => ['Río Torobamba', 'Manantial Local'],
                ],
                [
                    'distrito' => 'Vilcas Huamán',
                    'ubicacion_text' => 'Ayacucho - Vilcas Huamán - Vilcas Huamán',
                    'referencia' => 'Sector Pillucho S/N, a 1km de la Plaza de Armas de Vilcas Huamán',
                    'lat' => -13.653600,
                    'lng' => -73.953600,
                    'fuentes' => ['Canal de Riego Vilcas', 'Lluvia y Reservorio'],
                ],
                [
                    'distrito' => 'Cangallo',
                    'ubicacion_text' => 'Ayacucho - Cangallo - Cangallo',
                    'referencia' => 'Ribera del Río Pampas, Sector Huancapi Lote 15',
                    'lat' => -13.631100,
                    'lng' => -74.142200,
                    'fuentes' => ['Río Pampas', 'Canal Agrícola'],
                ],
                [
                    'distrito' => 'Carmen Alto, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Carmen Alto',
                    'referencia' => 'Sector Yanama S/N, parte baja del distrito',
                    'lat' => -13.172200,
                    'lng' => -74.221700,
                    'fuentes' => ['Agua Potable Agrícola', 'Pozo Subterráneo'],
                ],
                [
                    'distrito' => 'Chiara, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Chiara',
                    'referencia' => 'Comunidad de Manallasacc, Carretera Sur Km 22',
                    'lat' => -13.272200,
                    'lng' => -74.202800,
                    'fuentes' => ['Reservorio Chiara', 'Canal Cachi'],
                ],
                [
                    'distrito' => 'Vinchos, Huamanga',
                    'ubicacion_text' => 'Ayacucho - Huamanga - Vinchos',
                    'referencia' => 'Sector Casacancha, Vía Libertadores Km 45',
                    'lat' => -13.238889,
                    'lng' => -74.358333,
                    'fuentes' => ['Río Vinchos', 'Puquial Natural'],
                ],
            ];

            $tiposSuelo = ['franco', 'arcilloso', 'arenoso', 'limoso'];
            $nombresTerrenosBase = [
                'Fundo San Juan', 'Parcela Los Olivos', 'Chakra Santa Rosa',
                'Terreno El Molino', 'Fundo La Esperanza', 'Parcela Pampa Verde',
                'Chakra San José', 'Fundo Los Alisos', 'Terreno Vista Alegre',
                'Parcela El Pedregal', 'Fundo San Isidro', 'Chakra La Cantuta',
                'Terreno Bellavista', 'Fundo Santa Lucía', 'Parcela El Carmen',
            ];

            $terrenosCreados = 0;

            // 2. Registrar 5 Terrenos específicos para el usuario agricultor4_2@agrosys.com
            $usuarioEspecial = User::where('email', 'agricultor4_2@agrosys.com')->first();

            if ($usuarioEspecial) {
                $membresiaEsp = MiembroOrganizacion::where('usuario_id', $usuarioEspecial->id)
                    ->where('estado', 1)
                    ->first();
                $orgIdEsp = $membresiaEsp ? $membresiaEsp->organizacion_id : null;

                $terrenosAgricultor42 = [
                    [
                        'nombre' => 'Fundo San Juan de Quinua (Lote 1)',
                        'ubicacion_text' => 'Ayacucho - Huamanga - Quinua',
                        'referencia' => 'Km 15 Carretera a Quinua, cerca al Obelisco de la Pampa de Ayacucho',
                        'lat' => -13.058333,
                        'lng' => -74.138056,
                        'hectareas' => 3.5,
                        'tenencia' => 'propio',
                        'costo' => 0,
                        'suelo' => 'franco',
                        'agua' => 'Canal de Riego Quinua',
                    ],
                    [
                        'nombre' => 'Parcela El Molino de Luricocha (Lote 2)',
                        'ubicacion_text' => 'Ayacucho - Huanta - Luricocha',
                        'referencia' => 'Valle Frutícola de Luricocha, Sector Azángaro Lote 8',
                        'lat' => -12.936111,
                        'lng' => -74.250000,
                        'hectareas' => 5.0,
                        'tenencia' => 'alquilado',
                        'costo' => 2400.00,
                        'suelo' => 'arcilloso',
                        'agua' => 'Río Cachi',
                    ],
                    [
                        'nombre' => 'Chakra Santa Rosa de Socos (Lote 3)',
                        'ubicacion_text' => 'Ayacucho - Huamanga - Socos',
                        'referencia' => 'Comunidad Campesina de Socos, Sector Los Olivos S/N',
                        'lat' => -13.216667,
                        'lng' => -74.316667,
                        'hectareas' => 2.8,
                        'tenencia' => 'propio',
                        'costo' => 0,
                        'suelo' => 'limoso',
                        'agua' => 'Pozo Subterráneo',
                    ],
                    [
                        'nombre' => 'Fundo La Esperanza de Pacaycasa (Lote 4)',
                        'ubicacion_text' => 'Ayacucho - Huamanga - Pacaycasa',
                        'referencia' => 'Carretera Central Ayacucho-Huanta Km 12, Pampa de Pacaycasa',
                        'lat' => -13.052200,
                        'lng' => -74.223600,
                        'hectareas' => 4.2,
                        'tenencia' => 'alquilado',
                        'costo' => 1800.00,
                        'suelo' => 'arenoso',
                        'agua' => 'Río Yucay',
                    ],
                    [
                        'nombre' => 'Terreno Vista Alegre de Carmen Alto (Lote 5)',
                        'ubicacion_text' => 'Ayacucho - Huamanga - Carmen Alto',
                        'referencia' => 'Sector Yanama S/N, parte baja del distrito',
                        'lat' => -13.172200,
                        'lng' => -74.221700,
                        'hectareas' => 6.0,
                        'tenencia' => 'propio',
                        'costo' => 0,
                        'suelo' => 'franco',
                        'agua' => 'Agua Potable Agrícola',
                    ],
                ];

                foreach ($terrenosAgricultor42 as $tData) {
                    $poligonoArray = $this->generarPoligonoSegunHectareas($tData['lat'], $tData['lng'], $tData['hectareas']);
                    $esAlq = ($tData['tenencia'] === 'alquilado');

                    Terreno::create([
                        'organizacion_id' => $orgIdEsp,
                        'usuario_id' => $usuarioEspecial->id, // usuario_id asignado a agricultor4_2@agrosys.com
                        'nombre' => $tData['nombre'],
                        'ubicacion' => $tData['ubicacion_text'],
                        'direccion_referencia' => $tData['referencia'],
                        'latitud' => $tData['lat'],
                        'longitud' => $tData['lng'],
                        'poligono' => $poligonoArray,
                        'hectareas' => $tData['hectareas'],
                        'tipo_tenencia' => $tData['tenencia'],
                        'costo_alquiler_anual' => $tData['costo'],
                        'alquiler_modalidad' => $esAlq ? 'global' : 'global',
                        'alquiler_periodo' => $esAlq ? 'anual' : 'fecha',
                        'fecha_alquiler' => $esAlq ? Carbon::now()->subMonths(rand(1, 6)) : null,
                        'fecha_vencimiento_alquiler' => $esAlq ? Carbon::now()->addMonths(rand(6, 12)) : null,
                        'calidad_suelo' => $tData['suelo'],
                        'fuente_agua' => $tData['agua'],
                        'estado_terreno' => 'activo',
                        'estado' => 1,
                    ]);

                    $terrenosCreados++;
                }
            }

            // 3. Registrar terrenos para los demás usuarios
            foreach ($usuarios as $uIndex => $user) {
                if ($usuarioEspecial && $user->id === $usuarioEspecial->id) {
                    continue; // Ya se crearon sus 5 terrenos
                }

                $membresia = MiembroOrganizacion::where('usuario_id', $user->id)
                    ->where('estado', 1)
                    ->first();

                $orgId = $membresia ? $membresia->organizacion_id : null;

                for ($t = 1; $t <= 2; $t++) {
                    $ubicData = $ubicacionesAyacucho[($uIndex + $t) % count($ubicacionesAyacucho)];
                    $esAlquilado = ($t % 2 === 0);

                    $lat = $ubicData['lat'] + (rand(-100, 100) / 100000);
                    $lng = $ubicData['lng'] + (rand(-100, 100) / 100000);
                    $hectareas = round(rand(15, 90) / 10, 2);

                    $poligonoArray = $this->generarPoligonoSegunHectareas($lat, $lng, $hectareas);

                    $nombreBase = $nombresTerrenosBase[($uIndex + $t) % count($nombresTerrenosBase)];
                    $nombreTerreno = "{$nombreBase} de {$user->nombres} (Lote {$t})";

                    Terreno::create([
                        'organizacion_id' => $orgId,
                        'usuario_id' => $user->id,
                        'nombre' => $nombreTerreno,
                        'ubicacion' => $ubicData['ubicacion_text'],
                        'direccion_referencia' => $ubicData['referencia'],
                        'latitud' => $lat,
                        'longitud' => $lng,
                        'poligono' => $poligonoArray,
                        'hectareas' => $hectareas,
                        'tipo_tenencia' => $esAlquilado ? 'alquilado' : 'propio',
                        'costo_alquiler_anual' => $esAlquilado ? (float)rand(1200, 3600) : 0,
                        'alquiler_modalidad' => $esAlquilado ? (rand(0, 1) ? 'global' : 'por_campana') : 'global',
                        'alquiler_periodo' => $esAlquilado ? 'anual' : 'fecha',
                        'fecha_alquiler' => $esAlquilado ? Carbon::now()->subMonths(rand(1, 8)) : null,
                        'fecha_vencimiento_alquiler' => $esAlquilado ? Carbon::now()->addMonths(rand(4, 12)) : null,
                        'calidad_suelo' => $tiposSuelo[rand(0, 3)],
                        'fuente_agua' => $ubicData['fuentes'][array_rand($ubicData['fuentes'])],
                        'estado_terreno' => 'activo',
                        'estado' => 1,
                    ]);

                    $terrenosCreados++;
                }
            }

            DB::commit();

            if (isset($this->command)) {
                $this->command->info('====================================================');
                $this->command->info(' ¡TerrenosAyacuchoSeeder ejecutado con éxito!      ');
                $this->command->info('====================================================');
                $this->command->info(" Total Terrenos creados: {$terrenosCreados}");
                $this->command->info(' Usuario agricultor4_2@agrosys.com: 5 terrenos registrados en Ayacucho.');
                $this->command->info(' Ubicación: Región Ayacucho, Perú');
                $this->command->info(' ÚNICAMENTE TERRENOS (sin cultivos, labores ni ventas residuales).');
                $this->command->info(' Polígonos: Calculados en coordenadas proporcionales a las hectáreas.');
                $this->command->info('====================================================');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($this->command)) {
                $this->command->error('Error durante la ejecución de TerrenosAyacuchoSeeder: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Genera las coordenadas de un polígono cuadrado ajustado a la superficie en hectáreas,
     * centrado en la latitud y longitud especificadas en Ayacucho.
     */
    private function generarPoligonoSegunHectareas(float $latCenter, float $lngCenter, float $hectareas): array
    {
        // 1 hectárea = 10,000 m² (Lado del cuadrado en metros = sqrt(hectáreas * 10000))
        $ladoMetros = sqrt($hectareas * 10000);

        // Factores de conversión aproximados para latitud -13.16° (Ayacucho):
        // 1 grado latitud ≈ 111,000 m
        // 1 grado longitud ≈ 108,000 m
        $deltaLat = ($ladoMetros / 111000) / 2;
        $deltaLng = ($ladoMetros / 108000) / 2;

        return [
            [round($latCenter + $deltaLat, 7), round($lngCenter - $deltaLng, 7)], // Noroeste (NW)
            [round($latCenter + $deltaLat, 7), round($lngCenter + $deltaLng, 7)], // Noreste (NE)
            [round($latCenter - $deltaLat, 7), round($lngCenter + $deltaLng, 7)], // Sureste (SE)
            [round($latCenter - $deltaLat, 7), round($lngCenter - $deltaLng, 7)], // Suroeste (SW)
        ];
    }
}
