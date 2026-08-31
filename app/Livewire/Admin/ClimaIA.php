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

        $trendData = [
            'labels' => $history->reverse()->map(fn($h) => \Carbon\Carbon::parse($h->fecha_hora)->format('d/m'))->toArray(),
            'tempValues' => $history->reverse()->pluck('temperatura')->toArray(),
            'humValues' => $history->reverse()->pluck('humedad')->toArray(),
            'title' => 'Tendencias climáticas',
            'unit' => '°C / %'
        ];

        // Solo cultivos activos o planificados
        $cultivosActivos = $this->selectedTerrenoId
            ? Cultivo::where('terreno_id', $this->selectedTerrenoId)
                ->whereIn('estado', ['En crecimiento', 'Planificado'])
                ->with('detalleCatalogo')->get()
            : [];

        // Recomendaciones IA Generales para el Terreno
        $generalRecs = [];
        if ($latestWeather) {
            if ($latestWeather->prob_lluvia > 70) {
                $generalRecs[] = ['type' => 'Alerta Lluvia', 'msg' => 'Alta probabilidad de precipitación. Asegurar drenajes y suspender aplicaciones foliares.', 'priority' => 'Alta', 'color' => 'blue'];
            }
            if ($latestWeather->viento_kmh > 25) {
                $generalRecs[] = ['type' => 'Vientos Fuertes', 'msg' => 'Vientos detectados sobre 25km/h. Evitar fumigación por deriva.', 'priority' => 'Media', 'color' => 'amber'];
            }
        }

        if (empty($generalRecs)) {
            $generalRecs = [
                ['type' => 'Estado Óptimo', 'msg' => 'Condiciones estables para labores generales.', 'priority' => 'Baja', 'color' => 'emerald']
            ];
        }

        // Recomendaciones Específicas por Cultivo (IA basada en Catálogo)
        $cropRecs = [];
        if ($this->selectedCropId) {
            $c = Cultivo::with('detalleCatalogo')->find($this->selectedCropId);
            if ($c && $c->detalleCatalogo) {
                // Riego IA
                if ($currentWeather['humedad'] > 80) {
                    $cropRecs[] = ['type' => 'IA Riego', 'msg' => "Humedad alta (" . $currentWeather['humedad'] . "%). " . ($c->detalleCatalogo->instrucciones_base_riego ?: 'Reducir frecuencia de riego.'), 'priority' => 'Media', 'color' => 'blue'];
                }

                // Plagas IA
                if ($currentWeather['temp'] > 25 && $currentWeather['humedad'] > 70) {
                    $cropRecs[] = ['type' => 'IA Fitopatología', 'msg' => "Riesgo de plagas por calor/humedad. " . ($c->detalleCatalogo->instrucciones_base_plagas ?: 'Monitorear presencia de hongos.'), 'priority' => 'Alta', 'color' => 'rose'];
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
