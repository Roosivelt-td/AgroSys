<?php

namespace App\Livewire\Admin;

use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
#[Title('Clima IA')]
class ClimaIA extends Component
{
    #[Url]
    public $selectedTerrenoId = null;

    #[Url]
    public $selectedCropId = null;

    public $selectedOrgId = null;
    public $viewTimestamp = '';

    // Filtros de Búsqueda
    public $search = '';
    public $fTerrenoId = '';
    public $fCultivoId = '';
    public $fVariedad = '';
    public $fFecha = '';

    // Ubicación GPS en tiempo real
    public $userLat = null;
    public $userLng = null;
    public $useGPS = false;

    // Estados para Favoritos y Ver más tarde (Interacción estilo Movie18)
    public $favorites = [];
    public $savedItems = [];

    // Propiedad para datos del gráfico (compartida con JS)
    public $currentWeatherJS = [];

    public function toggleFavorite($id)
    {
        if (in_array($id, $this->favorites)) {
            $this->favorites = array_diff($this->favorites, [$id]);
        } else {
            $this->favorites[] = $id;
        }
    }

    public function toggleSave($id)
    {
        if (in_array($id, $this->savedItems)) {
            $this->savedItems = array_diff($this->savedItems, [$id]);
        } else {
            $this->savedItems[] = $id;
        }
    }

    public function selectTerreno($id)
    {
        $this->useGPS = false;
        $this->selectedTerrenoId = $id;
        $this->fTerrenoId = $id; // Sincronizar filtro
        $this->updatedSelectedTerrenoId($id);
    }

    public function updatedSelectedCropId($value)
    {
        if (!$value) return;

        $this->selectedCropId = $value;
        $crop = Cultivo::find($value);
        if ($crop) {
            $this->useGPS = false;
            $this->selectedTerrenoId = $crop->terreno_id;
            $this->fTerrenoId = $crop->terreno_id;
            $this->viewTimestamp = now()->getPreciseTimestamp(3);

            // Forzar actualización de clima si es necesario
            $weatherService = new \App\Services\WeatherService();
            $terreno = Terreno::find($crop->terreno_id);
            if ($terreno) {
                $weatherService->updateCurrentWeather($terreno);
                if ($terreno->latitud && $terreno->longitud) {
                    $this->dispatch('map-center-to', [
                        'lat' => (float)$terreno->latitud,
                        'lng' => (float)$terreno->longitud
                    ]);
                }
            }
        }
        $this->dispatch('refreshChart');
    }

    public function selectCrop($id)
    {
        $this->updatedSelectedCropId($id);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'fTerrenoId', 'fCultivoId', 'fVariedad', 'fFecha', 'selectedCropId']);
    }

    public function updateGPSLocation($lat, $lng)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->useGPS = true;

        // Buscar o crear un "terreno virtual" para guardar el historial de escaneos GPS del usuario
        $virtualTerreno = Terreno::firstOrCreate(
            [
                'usuario_id' => Auth::id(),
                'nombre' => '📍 Escáner GPS',
            ],
            [
                'latitud' => $lat,
                'longitud' => $lng,
                'tipo_tenencia' => 'propio',
                'estado_terreno' => 'inactivo',
                'hectareas' => 0,
            ]
        );

        $this->selectedTerrenoId = $virtualTerreno->id;

        // Actualizar coordenadas del terreno virtual al punto exacto del escaneo
        $virtualTerreno->update([
            'latitud' => $lat,
            'longitud' => $lng
        ]);

        $weatherService = new \App\Services\WeatherService();
        $weatherService->getWeatherByCoords($lat, $lng, $virtualTerreno->id);

        $this->dispatch('refreshChart');
    }

    public function mount()
    {
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())->where('estado', 1)->first();
        if ($membresia) $this->selectedOrgId = $membresia->organizacion_id;

        $this->viewTimestamp = now()->getPreciseTimestamp(3);
    }

    public function updatedSelectedTerrenoId($value = null)
    {
        $value = $value ?? $this->selectedTerrenoId;
        $this->selectedCropId = null;
        $this->viewTimestamp = now()->getPreciseTimestamp(3);

        if ($value) {
            $terreno = Terreno::find($value);
            if ($terreno) {
                // Actualizar clima en tiempo real
                $weatherService = new \App\Services\WeatherService();
                $weatherService->updateCurrentWeather($terreno);

                if ($terreno->latitud && $terreno->longitud) {
                    $this->dispatch('map-center-to', [
                        'lat' => (float)$terreno->latitud,
                        'lng' => (float)$terreno->longitud
                    ]);
                }
            }
        }

        $this->dispatch('refreshChart');
    }

    public function render()
    {
        $user = Auth::user();
        $allowedIds = [$user->id];

        $terrenos = Terreno::whereIn('usuario_id', $allowedIds)
            ->where('nombre', '!=', '📍 Escáner GPS') // Ocultar el virtual del mapa general
            ->when($this->selectedOrgId, fn($q) => $q->orWhere('organizacion_id', $this->selectedOrgId))
            ->get();

        // Fetch Real Weather Data
        $weatherService = new \App\Services\WeatherService();
        $latestWeather = null;
        $activeLat = -13.16; // Ayacucho default
        $activeLng = -74.22;
        $locationLabel = 'MICROCLIMA AYACUCHO';

        if ($this->useGPS && $this->userLat) {
            $activeLat = $this->userLat;
            $activeLng = $this->userLng;
            $locationLabel = 'GPS EN VIVO';
        } elseif ($this->selectedTerrenoId) {
            $terreno = Terreno::find($this->selectedTerrenoId);
            if ($terreno && $terreno->latitud && $terreno->longitud) {
                $activeLat = $terreno->latitud;
                $activeLng = $terreno->longitud;
                $locationLabel = strtoupper($terreno->nombre);
            }
        }

        // Obtener datos reales del servicio
        $latestWeather = $weatherService->getWeatherByCoords($activeLat, $activeLng, $this->selectedTerrenoId);

        $currentWeather = [
            'temp' => (float)($latestWeather->temperatura ?? 24),
            'humedad' => (int)($latestWeather->humedad ?? 65),
            'viento' => (float)($latestWeather->viento_kmh ?? 12),
            'presion' => (float)($latestWeather->presion_hpa ?? 1012),
            'prob_lluvia' => (int)($latestWeather->prob_lluvia ?? 0),
            'condicion' => strtoupper($latestWeather->condicion ?? 'SOLEADO'),
            'icon' => $this->getWeatherIcon($latestWeather->condicion ?? 'soleado'),
            'location_type' => $this->useGPS ? 'GPS EXACTO' : 'TERRENO',
            'location_name' => $locationLabel,
            'coords' => "{$activeLat}, {$activeLng}",
            'forecast' => $latestWeather->forecast ?? [],
            'hourly_data' => $latestWeather->hourly_data ?? ['labels' => [], 'temps' => [], 'hums' => []],
            'extras' => $latestWeather->extras ?? ['sunrise' => '--:--', 'sunset' => '--:--', 'uv' => 0],
            'daily_pronosticos' => $this->selectedTerrenoId
                ? \App\Models\ClimaPronostico::where('terreno_id', $this->selectedTerrenoId)->where('fecha', '>=', now()->toDateString())->orderBy('fecha', 'asc')->take(7)->get()
                : collect()
        ];

        // Sincronizar con propiedad pública para JS
        $this->currentWeatherJS = $currentWeather;

        // DISPARAR ACTUALIZACIÓN DE GRÁFICO CON DATOS FRESCOS
        $this->dispatch('refresh-chart-data', hourly: $currentWeather['hourly_data']);

        // Fetch Trend Data (Last 7 records)
        $history = $this->selectedTerrenoId
            ? \App\Models\ClimaRegistro::where('terreno_id', $this->selectedTerrenoId)
                ->orderBy('fecha_hora', 'desc')
                ->take(7)
                ->get()
            : collect();

        // VALIDACIÓN: Si no hay historial, generamos data de simulación para que el gráfico no se vea vacío
        if ($history->isEmpty()) {
            $trendData = [
                'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                'tempValues' => [18, 22, 19, 25, 21, 23, 20],
                'humValues' => [65, 70, 68, 75, 72, 80, 69],
                'title' => 'Tendencias Climáticas (Simulación)',
                'unit' => '°C / %'
            ];
        } else {
            $trendData = [
                'labels' => $history->reverse()->map(fn($h) => \Carbon\Carbon::parse($h->fecha_hora)->format('d/m'))->toArray(),
                'tempValues' => $history->reverse()->pluck('temperatura')->toArray(),
                'humValues' => $history->reverse()->pluck('humedad')->toArray(),
                'title' => 'Tendencias climáticas reales',
                'unit' => '°C / %'
            ];
        }

        // 2. Query de Cultivos ACTIVOS (Excluye Cosechados/Perdidos)
        $queryCrops = Cultivo::query()
            ->whereIn('estado', ['En crecimiento', 'Planificado'])
            ->whereHas('terreno', function($q) use ($allowedIds) {
                $q->whereIn('usuario_id', $allowedIds)
                  ->when($this->selectedOrgId, fn($sq) => $sq->orWhere('organizacion_id', $this->selectedOrgId));
            });

        // 3. Datos para Selects (Solo lo que existe y está activo para este usuario)
        $idsCatalogosActivos = (clone $queryCrops)->pluck('catalogo_cultivo_id')->unique();
        $catalogosExistentes = \App\Models\CatalogoCultivo::whereIn('id', $idsCatalogosActivos)->get();
        $variedadesExistentes = (clone $queryCrops)->whereNotNull('variedad')->pluck('variedad')->unique();

        // 4. Aplicar Filtros
        if ($this->search) {
            $queryCrops->where(fn($q) =>
                $q->where('nombre_lote', 'like', "%{$this->search}%")
                  ->orWhere('variedad', 'like', "%{$this->search}%")
                  ->orWhereHas('detalleCatalogo', fn($sq) => $sq->where('nombre', 'like', "%{$this->search}%"))
            );
        }
        if ($this->fTerrenoId) $queryCrops->where('terreno_id', $this->fTerrenoId);
        if ($this->fCultivoId) $queryCrops->where('catalogo_cultivo_id', $this->fCultivoId);
        if ($this->fVariedad) $queryCrops->where('variedad', $this->fVariedad);
        if ($this->fFecha) {
            $queryCrops->whereDate('fecha_siembra', '<=', $this->fFecha)
                       ->whereDate('fecha_cosecha_estimada', '>=', $this->fFecha);
        }

        $cultivosActivos = $queryCrops->with(['detalleCatalogo', 'terreno.latestClima'])->get();

        // 5. Determinar Terreno para Clima Real
        if ($this->selectedCropId) {
            $crop = Cultivo::find($this->selectedCropId);
            if ($crop) $this->selectedTerrenoId = $crop->terreno_id;
        } elseif ($this->fTerrenoId) {
            $this->selectedTerrenoId = $this->fTerrenoId;
        }

        // Recomendaciones IA Generales para el Terreno
        $agroBot = new \App\Services\AgroBotService();
        $iaRecs = $agroBot->analyzeWeather($currentWeather);

        // Normalizar: Asegurar que siempre sea un array de arrays (lista de recomendaciones)
        if (!empty($iaRecs) && !isset($iaRecs[0])) {
            $iaRecs = [$iaRecs];
        }

        $generalRecs = !empty($iaRecs) ? $iaRecs : [];

        if (empty($generalRecs)) {
            if ($latestWeather) {
                $probLluvia = $latestWeather->prob_lluvia ?? 0;
                $viento = $latestWeather->viento_kmh ?? 0;
                $temp = $latestWeather->temperatura ?? 0;

                if ($probLluvia > 60) {
                    $generalRecs[] = ['type' => 'Alerta Precipitación', 'msg' => 'Se detecta un ' . $probLluvia . '% de probabilidad de lluvia. Se recomienda proteger maquinaria y revisar sistemas de drenaje.', 'priority' => 'Alta', 'color' => 'blue'];
                }
                if ($viento > 20) {
                    $generalRecs[] = ['type' => 'Alerta de Viento', 'msg' => 'Vientos de ' . $viento . ' km/h detectados. Riesgo de deriva alto para fumigaciones foliares.', 'priority' => 'Media', 'color' => 'amber'];
                }
                if ($temp > 28) {
                    $generalRecs[] = ['type' => 'Estrés Térmico', 'msg' => 'Temperaturas elevadas detectadas. Aumentar monitoreo de humedad en suelo para evitar marchitamiento.', 'priority' => 'Media', 'color' => 'rose'];
                }
            }
        }

        if (empty($generalRecs)) {
            $generalRecs = [
                ['type' => 'Clima Estable', 'msg' => 'Las condiciones actuales son óptimas para labores de campo generales.', 'priority' => 'Baja', 'color' => 'emerald']
            ];
        }

        // Recomendaciones Específicas por Cultivo (IA basada en Catálogo)
        $cropRecs = [];
        if ($this->selectedCropId) {
            $c = Cultivo::with('detalleCatalogo')->find($this->selectedCropId);
            if ($c && $c->detalleCatalogo) {
                $iaCropRecs = $agroBot->analyzeWeather($currentWeather, [
                    'nombre' => $c->detalleCatalogo->nombre,
                    'variedad' => $c->variedad
                ]);

                // Normalizar recomendaciones de cultivo
                if (!empty($iaCropRecs) && !isset($iaCropRecs[0])) {
                    $iaCropRecs = [$iaCropRecs];
                }

                $cropRecs = !empty($iaCropRecs) ? $iaCropRecs : [];

                if (empty($cropRecs)) {
                    $cat = $c->detalleCatalogo;
                    // IA de Riego Dinámica (Fallback)
                    if ($currentWeather['humedad'] > 75) {
                        $cropRecs[] = [
                            'type' => 'IA Riego (Ahorro)',
                            'msg' => "Humedad ambiente alta ({$currentWeather['humedad']}%). " . ($cat->instrucciones_base_riego ?: 'Se sugiere suspender o reducir el riego programado para evitar asfixia radicular.'),
                            'priority' => 'Media',
                            'color' => 'blue'
                        ];
                    } elseif ($currentWeather['temp'] > 26 && $currentWeather['humedad'] < 40) {
                        $cropRecs[] = [
                            'type' => 'IA Riego (Urgente)',
                            'msg' => "Condiciones de sequedad extrema. Se recomienda riego de auxilio inmediato según: " . ($cat->instrucciones_base_riego ?: 'Aplicar riego profundo.'),
                            'priority' => 'Alta',
                            'color' => 'rose'
                        ];
                    }
                }
            }
        }

        // Datos para el Mapa
        $mapTerrenos = $terrenos->map(fn($t) => [
            'id' => $t->id,
            'nombre' => $t->nombre,
            'lat' => (float)$t->latitud,
            'lng' => (float)$t->longitud,
            'area' => $t->hectareas,
            'suelo' => $t->calidad_suelo,
            'poligono' => $t->poligono, // Incluir el polígono para que React lo dibuje
            'color' => ($t->usuario_id === $user->id) ? (($t->tipo_tenencia === 'propio') ? 'green' : 'cyan') : 'red',
            'es_mio' => $t->usuario_id === $user->id,
            'cultivo' => 'Zona Activa'
        ])->toArray();

        $actionPlan = [];
        if ($this->selectedCropId) {
            $c = $cultivosActivos->find($this->selectedCropId);
            if ($c) {
                // 1. RIEGO (Hoy)
                if ($currentWeather['humedad'] < 45 && $currentWeather['temp'] > 26) {
                    $actionPlan[] = ['icon' => 'fa-droplet', 'title' => 'Riego Urgente', 'desc' => 'Estrés hídrico detectado hoy. Se recomienda activar riego de auxilio.', 'color' => 'blue'];
                }

                // 2. FUMIGACIÓN (Próximos días)
                $nextDaysRain = $currentWeather['daily_pronosticos']->where('prob_lluvia', '>', 60)->first();
                if ($nextDaysRain) {
                    $actionPlan[] = ['icon' => 'fa-cloud-rain', 'title' => 'Alerta Lluvia', 'desc' => 'Lluvia detectada para el ' . $nextDaysRain->fecha->format('d/m') . '. Adelantar fumigaciones sistémicas.', 'color' => 'rose'];
                } elseif ($currentWeather['viento'] < 10) {
                    $actionPlan[] = ['icon' => 'fa-spray-can-sparkles', 'title' => 'Fumigar ahora', 'desc' => 'Vientos en calma (<10km/h). Momento ideal para aplicaciones foliares.', 'color' => 'emerald'];
                }

                // 3. SANIDAD (Pasado y Presente)
                if ($currentWeather['temp'] >= 18 && $currentWeather['temp'] <= 24 && $currentWeather['humedad'] > 75) {
                    $actionPlan[] = ['icon' => 'fa-microscope', 'title' => 'Alerta Rancha', 'desc' => 'Condiciones críticas de humedad para hongos. Aplicar preventivo.', 'color' => 'rose'];
                }

                // 4. COSECHA (Futuro)
                if ($c->fecha_cosecha_estimada) {
                    if ($c->fecha_cosecha_estimada->isPast()) {
                        $actionPlan[] = ['icon' => 'fa-basket-shopping', 'title' => 'Iniciar Cosecha', 'desc' => 'Cronograma técnico completado. Organizar logística de salida.', 'color' => 'amber'];
                    } elseif ($c->fecha_cosecha_estimada->diffInDays(now()) < 7) {
                        $actionPlan[] = ['icon' => 'fa-truck-fast', 'title' => 'Pre-Cosecha', 'desc' => 'Faltan ' . $c->fecha_cosecha_estimada->diffInDays(now()) . ' días. Suspender aplicaciones químicas de carencia.', 'color' => 'blue'];
                    }
                }
            }
        }

        if (empty($actionPlan)) {
            $actionPlan[] = ['icon' => 'fa-circle-check', 'title' => 'Planificación', 'desc' => 'Sin alertas urgentes. Seguir cronograma de manejo habitual.', 'color' => 'blue'];
        }

        return view('livewire.admin.clima-i-a', [
            'terrenos' => $terrenos,
            'cultivos' => $cultivosActivos,
            'catalogos' => $catalogosExistentes,
            'variedades' => $variedadesExistentes,
            'mapTerrenos' => $mapTerrenos,
            'current' => $currentWeather,
            'generalRecs' => $generalRecs,
            'cropRecs' => $cropRecs,
            'actionPlan' => $actionPlan,
            'trendData' => $trendData,
            'history' => $history
        ]);
    }

    private function getWeatherIcon($condition)
    {
        $condition = strtolower($condition);
        if (str_contains($condition, 'lluvia')) return 'fa-cloud-showers-heavy';
        if (str_contains($condition, 'nublado')) return 'fa-cloud';
        return 'fa-sun';
    }
}
