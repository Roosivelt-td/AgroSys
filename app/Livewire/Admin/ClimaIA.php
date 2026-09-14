<?php

namespace App\Livewire\Admin;

use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Clima IA')]
class ClimaIA extends Component
{
    public $selectedTerrenoId = null;
    public $selectedCropId = null;
    public $selectedOrgId = null;
    public $viewTimestamp = '';

    // Estados para Favoritos y Ver más tarde (Interacción estilo Movie18)
    public $favorites = [];
    public $savedItems = [];

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
        $this->selectedTerrenoId = $id;
        $this->updatedSelectedTerrenoId($id);
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
            if ($terreno && $terreno->latitud && $terreno->longitud) {
                $this->dispatch('map-center-to', [
                    'lat' => (float)$terreno->latitud,
                    'lng' => (float)$terreno->longitud
                ]);
            }
        }

        $this->dispatch('refreshChart');
    }

    public function render()
    {
        $user = Auth::user();
        $allowedIds = [$user->id];

        $terrenos = Terreno::whereIn('usuario_id', $allowedIds)
            ->when($this->selectedOrgId, fn($q) => $q->orWhere('organizacion_id', $this->selectedOrgId))
            ->get();

        // Fetch Real Weather Data
        $latestWeather = $this->selectedTerrenoId
            ? \App\Models\ClimaRegistro::where('terreno_id', $this->selectedTerrenoId)
                ->orderBy('fecha_hora', 'desc')
                ->first()
            : null;

        $currentWeather = [
            'temp' => $latestWeather->temperatura ?? 24,
            'humedad' => $latestWeather->humedad ?? 65,
            'viento' => $latestWeather->viento_kmh ?? 12,
            'presion' => $latestWeather->presion_hpa ?? 1012,
            'condicion' => strtoupper($latestWeather->condicion ?? 'SOLEADO'),
            'icon' => $this->getWeatherIcon($latestWeather->condicion ?? 'soleado')
        ];

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

        // Solo cultivos activos o planificados
        $cultivosActivos = $this->selectedTerrenoId
            ? Cultivo::where('terreno_id', $this->selectedTerrenoId)
                ->whereIn('estado', ['En crecimiento', 'Planificado'])
                ->with('detalleCatalogo')->get()
            : [];

        // Recomendaciones IA Generales para el Terreno
        $generalRecs = [];
        if ($latestWeather) {
            if ($latestWeather->prob_lluvia > 60) {
                $generalRecs[] = ['type' => 'Alerta Precipitación', 'msg' => 'Se detecta un ' . $latestWeather->prob_lluvia . '% de probabilidad de lluvia. Se recomienda proteger maquinaria y revisar sistemas de drenaje.', 'priority' => 'Alta', 'color' => 'blue'];
            }
            if ($latestWeather->viento_kmh > 20) {
                $generalRecs[] = ['type' => 'Alerta de Viento', 'msg' => 'Vientos de ' . $latestWeather->viento_kmh . ' km/h detectados. Riesgo de deriva alto para fumigaciones foliares.', 'priority' => 'Media', 'color' => 'amber'];
            }
            if ($latestWeather->temperatura > 28) {
                $generalRecs[] = ['type' => 'Estrés Térmico', 'msg' => 'Temperaturas elevadas detectadas. Aumentar monitoreo de humedad en suelo para evitar marchitamiento.', 'priority' => 'Media', 'color' => 'rose'];
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
                $cat = $c->detalleCatalogo;

                // IA de Riego Dinámica
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

                // IA de Sanidad Vegetal (Plagas/Hongos)
                if ($currentWeather['temp'] >= 18 && $currentWeather['temp'] <= 24 && $currentWeather['humedad'] > 80) {
                    $cropRecs[] = [
                        'type' => 'IA Fitopatología',
                        'msg' => "Condiciones ideales para la propagación de 'Rancha' (Phytophthora) en {$cat->nombre}. " . ($cat->instrucciones_base_plagas ?: 'Realizar monitoreo preventivo en el envés de las hojas.'),
                        'priority' => 'Alta',
                        'color' => 'rose'
                    ];
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

        return view('livewire.admin.clima-i-a', [
            'terrenos' => $terrenos,
            'cultivos' => $cultivosActivos,
            'mapTerrenos' => $mapTerrenos,
            'current' => $currentWeather,
            'generalRecs' => $generalRecs,
            'cropRecs' => $cropRecs,
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
