<?php

namespace App\Livewire\Admin;

use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\MiembroOrganizacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClimaIA extends Component
{
    public $selectedTerrenoId = null;
    public $selectedCropId = null;
    public $selectedOrgId = null;
    public $viewTimestamp = '';

    public function selectTerreno($id)
    {
        $this->selectedTerrenoId = $id;
        $this->updatedSelectedTerrenoId();
    }

    public function mount()
    {
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())->where('estado', 1)->first();
        if ($membresia) $this->selectedOrgId = $membresia->organizacion_id;

        $this->viewTimestamp = now()->getPreciseTimestamp(3);
    }

    public function updatedSelectedTerrenoId()
    {
        $this->selectedCropId = null;
        $this->viewTimestamp = now()->getPreciseTimestamp(3);
    }

    public function render()
    {
        $user = Auth::user();
        $allowedIds = [$user->id];

        $terrenos = Terreno::whereIn('usuario_id', $allowedIds)
            ->when($this->selectedOrgId, fn($q) => $q->orWhere('organizacion_id', $this->selectedOrgId))
            ->get();

        // Solo cultivos activos o planificados
        $cultivosActivos = $this->selectedTerrenoId
            ? Cultivo::where('terreno_id', $this->selectedTerrenoId)
                ->whereIn('estado', ['En crecimiento', 'Planificado'])
                ->with('detalleCatalogo')->get()
            : [];

        // Recomendaciones IA Generales para el Terreno
        $generalRecs = [
            ['type' => 'Riego General', 'msg' => 'Suspender riego el Miércoles por pronóstico de lluvia en toda la zona.', 'priority' => 'Alta', 'color' => 'blue'],
            ['type' => 'Vientos', 'msg' => 'Vientos > 15km/h detectados para el Jueves. Asegurar estructuras ligeras.', 'priority' => 'Media', 'color' => 'amber']
        ];

        // Recomendaciones Específicas por Cultivo (Lógica Simulada por Estado)
        $cropRecs = [];
        if ($this->selectedCropId) {
            $c = Cultivo::find($this->selectedCropId);
            if ($c) {
                if ($c->estado === 'Planificado') {
                    $cropRecs[] = ['type' => 'Preparación', 'msg' => "Condiciones de humedad de suelo ideales para iniciar arado en el lote de {$c->detalleCatalogo->nombre}.", 'priority' => 'Alta', 'color' => 'emerald'];
                } else {
                    $cropRecs[] = ['type' => 'Fumigación', 'msg' => "Ventana de aplicación fitosanitaria para {$c->detalleCatalogo->nombre} el Martes (Humedad < 70%).", 'priority' => 'Media', 'color' => 'amber'];
                    $cropRecs[] = ['type' => 'Nutrición', 'msg' => "Fase de crecimiento detectada. IA sugiere refuerzo de nitrógeno antes de la lluvia del Miércoles.", 'priority' => 'Alta', 'color' => 'blue'];
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
            'cultivo' => 'Zona Activa'
        ])->toArray();

        // Datos Clima para la Cabecera (Punto 2)
        $currentWeather = [
            'temp' => 24,
            'humedad' => 65,
            'viento' => 12,
            'presion' => 1012,
            'condicion' => 'SOLEADO',
            'icon' => 'fa-sun'
        ];

        // Datos del Gráfico (Punto 3)
        $trendData = [
            'labels' => ['2026-08-21', '2026-08-22', '2026-08-23', '2026-08-24', '2026-08-25', '2026-08-26', '2026-08-27'],
            'tempValues' => [22, 21, 22, 21.5, 20, 23, 20],
            'humValues' => [63, 67, 56, 67, 58, 56, 56],
            'title' => 'Tendencias climáticas (últimos 7 días)',
            'unit' => '°C / %'
        ];

        return view('livewire.admin.clima-i-a', [
            'terrenos' => $terrenos,
            'cultivos' => $cultivosActivos,
            'mapTerrenos' => $mapTerrenos,
            'current' => $currentWeather,
            'generalRecs' => $generalRecs,
            'cropRecs' => $cropRecs,
            'trendData' => $trendData
        ]);
    }
}
