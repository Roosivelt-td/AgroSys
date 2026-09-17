<?php

namespace App\Services;

use App\Models\Terreno;
use App\Models\ClimaRegistro;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WeatherService
{
    /**
     * API Key para OpenWeatherMap (Cargar desde .env si existe)
     */
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENWEATHER_API_KEY');
    }

    /**
     * Obtiene y guarda el clima actual para un terreno usando Open-Meteo (Gratis y Real).
     */
    public function updateCurrentWeather(Terreno $terreno)
    {
        return $this->getWeatherByCoords($terreno->latitud, $terreno->longitud, $terreno->id);
    }

    /**
     * Obtiene el clima por coordenadas exactas (Latitud y Longitud).
     */
    public function getWeatherByCoords($lat, $lng, $terrenoId = null)
    {
        if (!$lat || !$lng) return null;

        // Versión de caché v2 para evitar errores de objetos incompletos
        $cacheKey = "weather_v2_{$lat}_{$lng}_" . ($terrenoId ?? 'gps');

        $cached = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($lat, $lng, $terrenoId) {
            try {
            // Open-Meteo API: Alta precisión basada en coordenadas GPS exactas
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lng,
                'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code,surface_pressure,wind_speed_10m',
                'hourly' => 'temperature_2m,relative_humidity_2m,precipitation_probability',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max,precipitation_probability_max',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $res = $response->json();
                $current = $res['current'];
                $daily = $res['daily'];
                $hourly = $res['hourly'];

                $data = [
                    'terreno_id' => $terrenoId,
                    'fecha_hora' => now(),
                    'temperatura' => $current['temperature_2m'],
                    'humedad' => $current['relative_humidity_2m'],
                    'viento_kmh' => $current['wind_speed_10m'],
                    'presion_hpa' => $current['surface_pressure'],
                    'precipitacion_mm' => $current['precipitation'],
                    'condicion' => $this->interpretWeatherCode($current['weather_code']),
                    'prob_lluvia' => $hourly['precipitation_probability'][0] ?? 0,
                    'forecast' => $this->formatDailyForecast($daily),
                    'hourly_data' => $this->formatHourlyData($hourly),
                    'extras' => [
                        'sunrise' => \Carbon\Carbon::parse($daily['sunrise'][0])->format('H:i'),
                        'sunset' => \Carbon\Carbon::parse($daily['sunset'][0])->format('H:i'),
                        'uv' => $daily['uv_index_max'][0]
                    ]
                ];

                // Si es un terreno, lo guardamos en DB.
                if ($terrenoId) {
                    ClimaRegistro::create([
                        'terreno_id' => $terrenoId,
                        'fecha_hora' => $data['fecha_hora'],
                        'temperatura' => $data['temperatura'],
                        'humedad' => $data['humedad'],
                        'viento_kmh' => $data['viento_kmh'],
                        'presion_hpa' => $data['presion_hpa'],
                        'prob_lluvia' => $hourly['precipitation_probability'][0] ?? 0,
                        'precipitacion_mm' => $data['precipitacion_mm'],
                        'condicion' => $data['condicion'],
                    ]);

                    // Guardar Pronóstico de 7 días
                    foreach ($data['forecast'] as $i => $f) {
                        \App\Models\ClimaPronostico::updateOrCreate(
                            ['terreno_id' => $terrenoId, 'fecha' => \Carbon\Carbon::parse($daily['time'][$i])->toDateString()],
                            [
                                'temp_max' => $f['temp_max'],
                                'temp_min' => $f['temp_min'],
                                'prob_lluvia' => $daily['precipitation_probability_max'][$i] ?? 0,
                                'condicion' => $f['condition'],
                                'icon' => $f['icon']
                            ]
                        );
                    }
                }

                return $data;
            }
        } catch (\Exception $e) {
            Log::error("Error Open-Meteo Coords: " . $e->getMessage());
        }

        return null;
        });

        return $cached ? (object)$cached : null;
    }

    /**
     * Formatea los datos diarios para la vista tipo móvil.
     */
    protected function formatDailyForecast($daily)
    {
        $forecast = [];
        for ($i = 0; $i < 7; $i++) {
            $date = \Carbon\Carbon::parse($daily['time'][$i]);
            $forecast[] = [
                'day_name' => ($i === 0) ? 'Hoy' : $date->translatedFormat('D'),
                'full_date' => $date->translatedFormat('d M'),
                'temp_max' => round($daily['temperature_2m_max'][$i]),
                'temp_min' => round($daily['temperature_2m_min'][$i]),
                'condition' => $this->interpretWeatherCode($daily['weather_code'][$i]),
                'icon' => $this->getWeatherIconByCode($daily['weather_code'][$i])
            ];
        }
        return $forecast;
    }

    /**
     * Datos por hora para el gráfico de tendencias.
     */
    protected function formatHourlyData($hourly)
    {
        return [
            'labels' => collect($hourly['time'])->take(24)->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i'))->toArray(),
            'temps' => collect($hourly['temperature_2m'])->take(24)->toArray(),
            'hums' => collect($hourly['relative_humidity_2m'])->take(24)->toArray()
        ];
    }

    public function getWeatherIconByCode($code)
    {
        return match($code) {
            0 => 'fa-sun',
            1, 2, 3 => 'fa-cloud-sun',
            45, 48 => 'fa-smog',
            51, 53, 55 => 'fa-cloud-rain',
            61, 63, 65 => 'fa-cloud-showers-heavy',
            71, 73, 75 => 'fa-snowflake',
            80, 81, 82 => 'fa-cloud-bolt',
            default => 'fa-cloud',
        };
    }

    /**
     * Traduce códigos WMO de Open-Meteo a texto legible.
     */
    protected function interpretWeatherCode($code)
    {
        return match($code) {
            0 => 'Despejado',
            1, 2, 3 => 'Parcialmente nublado',
            45, 48 => 'Niebla',
            51, 53, 55 => 'Llovizna',
            61, 63, 65 => 'Lluvia',
            71, 73, 75 => 'Nieve',
            80, 81, 82 => 'Chubascos',
            95, 96, 99 => 'Tormenta eléctrica',
            default => 'Estable',
        };
    }

    protected function createMockRecord(Terreno $terreno)
    {
        return ClimaRegistro::create([
            'terreno_id' => $terreno->id,
            'fecha_hora' => now(),
            'temperatura' => rand(15, 30),
            'humedad' => rand(40, 90),
            'viento_kmh' => rand(5, 30),
            'presion_hpa' => 1013,
            'condicion' => 'Soleado (Simulado)',
        ]);
    }
}
