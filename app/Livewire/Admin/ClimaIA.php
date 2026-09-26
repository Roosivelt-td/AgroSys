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

    public function updatedFTerrenoId($value)
    {
        // 1 -> Prepara 2, 3 y 4 (Reset de cascada)
        $this->fCultivoId = '';
        $this->fVariedad = '';
        $this->selectedCropId = null;
        $this->selectedTerrenoId = $value ?: null;

        if ($value) {
            $this->updatedSelectedTerrenoId($value);
        }
    }

    public function updatedFCultivoId($value)
    {
        // 2 -> Prepara 3 y 4
        $this->fVariedad = '';
        $this->selectedCropId = null;
    }

    public function updatedFVariedad($value)
    {
        // 3 -> Prepara 4
        $this->selectedCropId = null;
    }

    public function updatedFFecha($value)
    {
        $this->selectedCropId = null;
    }

    public function selectCrop($id)
    {
        // 4 -> Selección final
        if (!$id) {
            $this->selectedCropId = null;
            return;
        }

        $this->selectedCropId = $id;
        $crop = Cultivo::find($id);
        if ($crop) {
            // Sincronización inversa para que los selects reflejen la realidad del lote
            $this->fTerrenoId = (string)$crop->terreno_id;
            $this->fCultivoId = (string)$crop->catalogo_cultivo_id;
            $this->fVariedad = $crop->variedad;
            $this->selectedTerrenoId = $crop->terreno_id;

            // Disparar actualización de UI y Mapas
            $this->updatedSelectedCropId($id);
        }
    }

    public function updatedSelectedCropId($value)
    {
        if (!$value) return;
        $this->aiAnalysis = "Esperando análisis de IA..."; // Reset para disparar carga en render

        $this->selectedCropId = $value;
        $crop = Cultivo::find($value);
        if ($crop) {
            $this->useGPS = false;
            $this->selectedTerrenoId = $crop->terreno_id;
            $this->viewTimestamp = now()->getPreciseTimestamp(3);

            // Forzar actualización de clima
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

        // 2. Query Base de Cultivos ACTIVOS
        $baseQuery = Cultivo::query()
            ->whereIn('estado', ['En crecimiento', 'Planificado', 'Cosecha'])
            ->whereHas('terreno', function($q) use ($allowedIds) {
                $q->whereIn('usuario_id', $allowedIds)
                  ->when($this->selectedOrgId, fn($sq) => $sq->orWhere('organizacion_id', $this->selectedOrgId));
            });

        // 3. Datos Dinámicos Multidireccionales (Filtros Cruzados 1 -> 2 -> 3 -> 4)

        // Select 1 (TERRENO): Filtrado por Cultivo y Variedad
        $terrenoSelectQuery = Terreno::whereIn('usuario_id', $allowedIds)
            ->where('nombre', '!=', '📍 Escáner GPS')
            ->when($this->selectedOrgId, fn($q) => $q->orWhere('organizacion_id', $this->selectedOrgId));
        if ($this->fCultivoId || $this->fVariedad) {
            $terrenoSelectQuery->whereHas('cultivos', function($q) {
                if ($this->fCultivoId) $q->where('catalogo_cultivo_id', $this->fCultivoId);
                if ($this->fVariedad) $q->where('variedad', $this->fVariedad);
                $q->whereIn('estado', ['En crecimiento', 'Planificado', 'Cosecha']);
            });
        }
        $terrenosOptions = $terrenoSelectQuery->get();

        // Select 2 (CULTIVO): Depende de la selección de Terreno (1) y Variedad (3)
        $catalogQuery = clone $baseQuery;
        if ($this->fTerrenoId) $catalogQuery->where('terreno_id', $this->fTerrenoId);
        if ($this->fVariedad) $catalogQuery->where('variedad', $this->fVariedad);
        $idsCatalogosActivos = $catalogQuery->pluck('catalogo_cultivo_id')->unique();
        $catalogosExistentes = \App\Models\CatalogoCultivo::whereIn('id', $idsCatalogosActivos)->get();

        // Select 3 (VARIEDAD): Depende de Terreno (1) y Cultivo (2)
        $variedadQuery = clone $baseQuery;
        if ($this->fTerrenoId) $variedadQuery->where('terreno_id', $this->fTerrenoId);
        if ($this->fCultivoId) $variedadQuery->where('catalogo_cultivo_id', $this->fCultivoId);
        $variedadesExistentes = $variedadQuery->whereNotNull('variedad')->pluck('variedad')->unique();

        // Select 4 (LOTES): Aplicación de todos los filtros acumulados
        $queryCrops = clone $baseQuery;
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

        // Determinar Terreno para Clima Real (Sincronización de Contexto)
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
        $mapTerrenos = $terrenos->map(function($t) use ($user) {
            $direccionText = $t->ubicacion ?: ($t->direccion_referencia ?: 'Sin dirección registrada');
            return [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'ubicacion' => $t->ubicacion,
                'direccion_referencia' => $t->direccion_referencia,
                'label' => "{$t->nombre} - {$direccionText} (" . number_format($t->hectareas, 2) . " HA)",
                'lat' => (float)$t->latitud,
                'lng' => (float)$t->longitud,
                'area' => $t->hectareas,
                'suelo' => $t->calidad_suelo,
                'poligono' => is_string($t->poligono) ? json_decode($t->poligono, true) : $t->poligono,
                'color' => ($t->usuario_id === $user->id) ? (($t->tipo_tenencia === 'propio') ? 'green' : 'cyan') : 'red',
                'es_mio' => $t->usuario_id === $user->id,
                'cultivo' => 'Zona Activa'
            ];
        })->toArray();

        // Datos de Inversión y Detalles para el Bloque 4 (Calculados antes de la IA para Insights)
        $investmentStats = null;
        if ($this->selectedCropId) {
            $crop = Cultivo::with(['labores.insumos.detalleCatalogo', 'labores.manoDeObra', 'labores.maquinaria', 'detalleCatalogo'])->find($this->selectedCropId);
            if ($crop) {
                $totalInversion = $crop->labores->sum('costo_total');
                $costoInsumos = 0;
                $costoPersonal = $crop->labores->sum('costo_mano_obra_total');
                $costoMaquinaria = $crop->labores->sum('costo_maquinaria_total');
                $costoAlquiler = $crop->terreno->costo_alquiler_anual ?? 0;

                $desgloseInsumos = ['fertilizantes' => 0, 'plaguicidas' => 0, 'otros' => 0];
                foreach ($crop->labores as $labor) {
                    foreach ($labor->insumos as $insumo) {
                        $costo = $insumo->cantidad * $insumo->precio_unitario;
                        $costoInsumos += $costo;
                        $cat = strtolower($insumo->detalleCatalogo->categoria ?? 'otros');
                        if (str_contains($cat, 'fertiliz')) $desgloseInsumos['fertilizantes'] += $costo;
                        elseif (str_contains($cat, 'plagui') || str_contains($cat, 'insecti')) $desgloseInsumos['plaguicidas'] += $costo;
                        else $desgloseInsumos['otros'] += $costo;
                    }
                }

                $totalGlobal = $totalInversion + $costoAlquiler;
                $investmentStats = [
                    'total' => $totalGlobal,
                    'insumos' => $costoInsumos,
                    'personal' => $costoPersonal,
                    'maquinaria' => $costoMaquinaria,
                    'alquiler' => $costoAlquiler,
                    'desglose_insumos' => $desgloseInsumos,
                    'plantas' => $crop->plantas_estimadas,
                    'rendimiento_esperado' => $crop->rendimiento_esperado_tn_ha,
                    'dias_cultivo' => $crop->fecha_siembra ? (int)floor($crop->fecha_siembra->diffInDays(now())) : 0,
                    'dias_totales' => $crop->fecha_siembra && $crop->fecha_cosecha_estimada ? (int)floor($crop->fecha_siembra->diffInDays($crop->fecha_cosecha_estimada)) : 120,
                ];
            }
        }

        $actionPlan = [
            'critico' => [],   // Riesgos Inmediatos
            'pendiente' => [],  // Estrategia Siguiente (Basado en JSON)
            'restriccion' => [], // Prevención (Lo que el clima prohíbe)
            'ia_insights' => []  // Análisis Predictivo
        ];

        if ($this->selectedCropId) {
            $c = Cultivo::with(['detalleCatalogo', 'terreno'])->find($this->selectedCropId);
            if ($c) {
                // 1. CARGAR CEREBRO ESTRATÉGICO (JSON)
                $jsonPath = database_path('migrations/procesos_cultivos_ia.json');
                $procesosData = json_decode(file_get_contents($jsonPath), true);

                $cultivoJson = null;
                $etapaActual = null;

                $nombreBusqueda = \Illuminate\Support\Str::ascii(mb_strtolower($c->detalleCatalogo->nombre));
                $cultivoJson = collect($procesosData['cultivos'])->first(function($item) use ($nombreBusqueda) {
                    $idNorm = \Illuminate\Support\Str::ascii(mb_strtolower($item['id']));
                    $nombreNorm = \Illuminate\Support\Str::ascii(mb_strtolower($item['nombre']));
                    return str_contains($nombreBusqueda, $idNorm) || str_contains($nombreBusqueda, $nombreNorm) || str_contains($idNorm, $nombreBusqueda);
                });

                if (!$cultivoJson) {
                    $cultivoJson = [
                        'id' => \Illuminate\Support\Str::slug($c->detalleCatalogo->nombre),
                        'nombre' => $c->detalleCatalogo->nombre,
                        'duracion_dias' => ['min' => 90, 'max' => 150],
                        'etapas' => [
                            ['id' => 'desarrollo', 'nombre' => 'Crecimiento y Desarrollo', 'inicio_dia' => 0, 'fin_dia' => 180, 'labores' => [
                                ['labor_id' => 'riego', 'nombre' => 'Monitoreo Hídrico', 'aplica' => true, 'depende_de_clima' => true, 'variables_climaticas' => ['temperatura', 'precipitacion'], 'accion_si_clima_no_adecuado' => 'ajustar riego'],
                                ['labor_id' => 'fumigar', 'nombre' => 'Protección Fitosanitaria', 'aplica' => true, 'depende_de_clima' => true, 'variables_climaticas' => ['viento', 'precipitacion'], 'accion_si_clima_no_adecuado' => 'posponer por viento/lluvia']
                            ]]
                        ]
                    ];
                }

                $edadDias = $c->fecha_siembra ? (int)floor($c->fecha_siembra->diffInDays(now())) : 0;

                $etapaActual = collect($cultivoJson['etapas'])->first(function($etapa) use ($edadDias) {
                    return $edadDias >= $etapa['inicio_dia'] && $edadDias <= $etapa['fin_dia'];
                }) ?: ($cultivoJson['etapas'][0] ?? null);

                    if ($etapaActual) {
                        foreach($etapaActual['labores'] as $labor) {
                            $debeHacerse = true;
                            $motivoRestriccion = "";

                            if ($labor['depende_de_clima'] ?? false) {
                                foreach(($labor['variables_climaticas'] ?? []) as $var) {
                                    if ($var == 'viento' && $currentWeather['viento'] > 15) {
                                        $debeHacerse = false;
                                        $motivoRestriccion = "Vientos de ".round($currentWeather['viento'])." km/h detectados. Riesgo de deriva.";
                                    }
                                    if ($var == 'precipitacion') {
                                        $probLluvia = $currentWeather['prob_lluvia'] ?? 0;
                                        $lluviaFutura = collect($currentWeather['forecast'])->where('prob_lluvia', '>', 60)->first();
                                        if ($probLluvia > 50 || $lluviaFutura) {
                                            $debeHacerse = false;
                                            $motivoRestriccion = "Probabilidad de lluvia alta (" . ($lluviaFutura ? $lluviaFutura['day_name'] : 'hoy') . ").";
                                        }
                                    }
                                }
                            }

                            if ($debeHacerse) {
                                $actionPlan['pendiente'][] = [
                                    'icon' => $this->getLaborIcon($labor['labor_id']),
                                    'title' => strtoupper($labor['nombre']),
                                    'desc' => "Día {$edadDias}: Etapa de {$etapaActual['nombre']}. La IA recomienda iniciar esta labor aprovechando las condiciones climáticas actuales.",
                                    'color' => 'blue'
                                ];
                            } else {
                                $actionPlan['restriccion'][] = [
                                    'icon' => 'fa-hand-dots',
                                    'title' => 'PAUSAR: ' . strtoupper($labor['nombre']),
                                    'desc' => "RESTRICCIÓN: {$motivoRestriccion} Según el proceso de {$cultivoJson['nombre']}, se debe " . ($labor['accion_si_clima_no_adecuado'] ?? 'reevaluar') . ".",
                                    'color' => 'amber'
                                ];
                            }
                        }
                    }

                // 4. ESTRATEGIA DE COORDINACIÓN (CEREBRO AGROBOT)
                // Ejecutar solo si no tenemos un análisis reciente para evitar bucles infinitos en render
                if ($this->aiAnalysis === "Esperando análisis de IA...") {
                    $this->askLocalAI($c, $cultivoJson, $etapaActual, $currentWeather, $investmentStats, $history);
                }

                // 5. ALERTAS CRÍTICAS DE SUPERVIVENCIA (INDIFERENTE AL JSON)
                if ($currentWeather['temp'] > 28) {
                    $actionPlan['critico'][] = ['icon' => 'fa-fire-orange', 'title' => 'ESTRÉS TÉRMICO', 'desc' => 'Calor extremo detectado. Riesgo de marchitamiento. Aumentar frecuencia de monitoreo hídrico.', 'color' => 'rose'];
                }

                // INSIGHTS DE RENDIMIENTO
                $porcentajeCiclo = $investmentStats ? round(($investmentStats['dias_cultivo'] / max(1, $investmentStats['dias_totales'])) * 100) : 0;
                if ($porcentajeCiclo > 0) {
                    $diasRestantes = (int)floor($investmentStats['dias_totales'] - $investmentStats['dias_cultivo']);
                    $actionPlan['ia_insights'][] = [
                        'icon' => 'fa-microchip',
                        'title' => 'ESTADO DEL CICLO',
                        'desc' => "Progreso biológico: {$porcentajeCiclo}%. La IA proyecta la cosecha para dentro de {$diasRestantes} días aproximadamente.",
                        'color' => 'emerald'
                    ];
                }
            }
        }

        // Fetch Labores for the selected crop
        $laboresCultivo = collect();
        if ($this->selectedCropId) {
            $laboresCultivo = \App\Models\Labor::with('detalleCatalogo')
                ->where('cultivo_id', $this->selectedCropId)
                ->orderBy('fecha_realizacion', 'desc')
                ->get();
        }

        return view('livewire.admin.clima-i-a', [
            'terrenosOptions' => $terrenosOptions,
            'catalogos' => $catalogosExistentes,
            'variedades' => $variedadesExistentes,
            'cultivos' => $cultivosActivos,
            'mapTerrenos' => $mapTerrenos,
            'current' => $currentWeather,
            'generalRecs' => $generalRecs,
            'cropRecs' => $cropRecs,
            'actionPlan' => $actionPlan,
            'trendData' => $trendData,
            'history' => $history,
            'investmentStats' => $investmentStats,
            'laboresCultivo' => $laboresCultivo,
            'aiAnalysis' => $this->aiAnalysis
        ]);
    }

    public $aiAnalysis = "Esperando análisis de IA...";



    private function askLocalAI($crop, $jsonProfile, $stage, $weather, $stats, $climateHistory)
    {
        $agroBot = new \App\Services\AgroBotService();

        // 1. Historial de Labores (MySQL)
        $historialLabores = $crop->labores()->where('estado', 'Completada')->orderBy('fecha_realizacion', 'desc')->take(5)->get()
            ->map(fn($l) => "- {$l->fecha_realizacion->format('d/m/Y')}: {$l->detalleCatalogo->nombre}")->implode("\n");

        // 2. Historial de Clima (Últimos días registrados)
        $historialClima = $climateHistory->map(fn($h) => "- " . \Carbon\Carbon::parse($h->fecha_hora)->format('d/m/Y H:i') . ": {$h->temperatura}°C, {$h->humedad}%, {$h->viento_kmh}km/h ({$h->condicion})")->implode("\n");

        $context = [
            'json_profile' => $jsonProfile,
            'historial_labores' => $historialLabores,
            'historial_clima' => $historialClima,
            'crop_name' => "{$crop->detalleCatalogo->nombre} (" . ($crop->variedad ?: 'Común') . ")",
            'dias_cultivo' => (int)floor($stats['dias_cultivo'] ?? 0),
            'dias_totales' => (int)floor($stats['dias_totales'] ?? 120),
            'etapa_nombre' => $stage['nombre'] ?? 'Crecimiento y Desarrollo',
            'weather' => $weather,
            'stats' => $stats
        ];

        $this->aiAnalysis = $agroBot->coordinateStrategicPlan($context);
    }

    private function getLaborIcon($id)
    {
        $icons = [
            'preparar' => 'fa-tractor',
            'siembra' => 'fa-seedling',
            'riego' => 'fa-droplet',
            'fumigar' => 'fa-spray-can-sparkles',
            'aporque' => 'fa-mountain',
            'deshierbe' => 'fa-hand-holding-plant',
            'abonar' => 'fa-vial-virus',
            'cosechar' => 'fa-basket-shopping'
        ];
        return $icons[$id] ?? 'fa-circle-dot';
    }

    private function getWeatherIcon($condition)
    {
        $condition = strtolower($condition);
        if (str_contains($condition, 'lluvia')) return 'fa-cloud-showers-heavy';
        if (str_contains($condition, 'nublado')) return 'fa-cloud';
        return 'fa-sun';
    }
}
