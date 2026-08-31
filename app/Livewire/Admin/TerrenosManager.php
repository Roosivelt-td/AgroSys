<?php

namespace App\Livewire\Admin;

use App\Models\Terreno;
use App\Models\MiembroOrganizacion;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Terrenos')]
class TerrenosManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterCrop = '';
    public $filterArea = '';
    public $selectedOrgId = null;
    public $activeLandId = null; // Terreno seleccionado para vista detallada
    public $chartPeriod = 'mes'; // mes o año

    // Campos de la Base de Datos
    public $landId = null;
    public $landName = '';
    public $landLocation = '';
    public $landDirRef = '';
    public $landLat = '';
    public $landLng = '';
    public $landArea = '';
    public $landPolygon = null; // Almacena el JSON de puntos
    public $landTenure = 'propio';
    public $landRentCost = 0;
    public $landRentMod = 'global';
    public $landRentPeriod = 'fecha';
    public $landRentStart = '';
    public $landRentEnd = '';
    public $landSoil = 'franco';
    public $landWater = 'Riego por goteo';
    public $landStatus = 'activo';
    public $landPhoto;
    public $currentPhotoPath;

    protected $queryString = ['search', 'filterCrop', 'filterArea'];

    public function mount()
    {
        // Detectar si el usuario pertenece a una organización activa
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('estado', 1)
            ->first();

        if ($membresia) {
            $this->selectedOrgId = $membresia->organizacion_id;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'landId', 'landName', 'landLocation', 'landDirRef', 'landLat', 'landLng', 'landPolygon',
            'landArea', 'landRentStart', 'landRentEnd', 'landPhoto', 'currentPhotoPath'
        ]);
        $this->landRentCost = 0;
        $this->landTenure = 'propio';
        $this->landRentMod = 'global';
        $this->landRentPeriod = 'fecha';
        $this->landSoil = 'franco';
        $this->landWater = 'Riego por goteo';
        $this->landStatus = 'activo';
    }

    public function save()
    {
        $this->validate([
            'landName' => 'required|string|max:150',
            'landArea' => 'required|numeric|min:0.1',
            'landLat' => 'required',
            'landLng' => 'required',
            'landPolygon' => 'required',
            'landPhoto' => 'nullable|image|max:5120',
            'landRentCost' => 'required_if:landTenure,alquilado|numeric|min:0',
            'landRentStart' => 'required_if:landTenure,alquilado',
            'landRentEnd' => 'required_if:landTenure,alquilado',
        ]);

        $user = Auth::user();
        $photoPath = $this->currentPhotoPath;

        if ($this->landPhoto) {
            $fileData = AgroStorageService::storeUserFile($this->landPhoto, $user, 'terreno', $this->selectedOrgId);
            $photoPath = $fileData['ruta_completa'];
            ArchivoMultimedia::create($fileData);
        }

        $data = [
            'nombre' => $this->landName,
            'ubicacion' => $this->landLocation,
            'direccion_referencia' => $this->landDirRef,
            'latitud' => $this->landLat,
            'longitud' => $this->landLng,
            'poligono' => is_string($this->landPolygon) ? json_decode($this->landPolygon, true) : $this->landPolygon,
            'hectareas' => $this->landArea,
            'tipo_tenencia' => $this->landTenure,
            'costo_alquiler_anual' => ($this->landTenure === 'alquilado') ? $this->landRentCost : 0,
            'alquiler_modalidad' => ($this->landTenure === 'alquilado') ? $this->landRentMod : 'global',
            'alquiler_periodo' => ($this->landTenure === 'alquilado') ? $this->landRentPeriod : 'fecha',
            'fecha_alquiler' => ($this->landTenure === 'alquilado' && $this->landRentStart) ? Carbon::parse($this->landRentStart) : null,
            'fecha_vencimiento_alquiler' => ($this->landTenure === 'alquilado' && $this->landRentEnd) ? Carbon::parse($this->landRentEnd) : null,
            'calidad_suelo' => $this->landSoil,
            'fuente_agua' => $this->landWater,
            'estado_terreno' => $this->landStatus,
            'foto_path' => $photoPath,
            'usuario_id' => $user->id,
            'organizacion_id' => $this->selectedOrgId,
            'estado' => 1
        ];

        if ($this->landId) {
            $terreno = Terreno::where('usuario_id', $user->id)->orWhere('organizacion_id', $this->selectedOrgId)->findOrFail($this->landId);
            $terreno->update($data);
            $msg = "Terreno actualizado.";
        } else {
            Terreno::create($data);
            $msg = "Terreno registrado con éxito.";
        }

        $this->dispatch('close-modal', 'modal-add-terrain');
        $this->resetForm();
        session()->flash('status', $msg);
    }

    public function edit($id)
    {
        $t = Terreno::findOrFail($id);
        $this->landId = $t->id;
        $this->landName = $t->nombre;
        $this->landLocation = $t->ubicacion;
        $this->landDirRef = $t->direccion_referencia;
        $this->landLat = $t->latitud;
        $this->landLng = $t->longitud;
        $this->landPolygon = is_array($t->poligono) ? json_encode($t->poligono) : $t->poligono;
        $this->landArea = $t->hectareas;
        $this->landTenure = $t->tipo_tenencia;
        $this->landRentCost = $t->costo_alquiler_anual;
        $this->landRentMod = $t->alquiler_modalidad ?? 'global';
        $this->landRentPeriod = $t->alquiler_periodo ?? 'fecha';
        $this->landRentStart = $t->fecha_alquiler ? $t->fecha_alquiler->format('Y-m-d') : '';
        $this->landRentEnd = $t->fecha_vencimiento_alquiler ? $t->fecha_vencimiento_alquiler->format('Y-m-d') : '';
        $this->landSoil = $t->calidad_suelo;
        $this->landWater = $t->fuente_agua;
        $this->landStatus = $t->estado_terreno;
        $this->currentPhotoPath = $t->foto_path;
        $this->selectedOrgId = $t->organizacion_id;

        $this->dispatch('open-modal', 'modal-add-terrain');
    }

    public function delete($id)
    {
        Terreno::findOrFail($id)->delete();
        session()->flash('status', 'Terreno eliminado.');
    }

    public function render()
    {
        $user = Auth::user();
        $allowedUserIds = [$user->id];

        // Obtener organizaciones activas del usuario para el formulario
        $misOrganizaciones = $user->membresias()->where('estado', 1)->with('organizacion')->get()->pluck('organizacion');

        // Lógica de visibilidad por Rol y Organización
        if ($this->selectedOrgId) {
            $miembro = $user->membresias()->where('organizacion_id', $this->selectedOrgId)->where('estado', 1)->first();

            if ($user->rol_id === 1) {
                // Super Admin ve todo de la organización seleccionada + sus personales
                $baseQuery = Terreno::where(function($q) use ($user) {
                    $q->where('organizacion_id', $this->selectedOrgId)
                      ->orWhere('usuario_id', $user->id);
                });
            } elseif ($miembro) {
                $esAdmin = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->where('estado', 1)->exists();
                $esSupervisor = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))->where('estado', 1)->exists();

                if ($esAdmin) {
                    // Admin de Org ve todo de su org + sus personales
                    $baseQuery = Terreno::where(function($q) use ($user) {
                        $q->where('organizacion_id', $this->selectedOrgId)
                          ->orWhere('usuario_id', $user->id);
                    });
                } elseif ($esSupervisor) {
                    $assignedIds = $user->getIdsAgricultoresAsignados($this->selectedOrgId);
                    $allowedUserIds = array_merge($allowedUserIds, $assignedIds);
                    // Supervisor ve lo asignado + lo suyo
                    $baseQuery = Terreno::where(function($q) use ($allowedUserIds, $user) {
                        $q->whereIn('usuario_id', $allowedUserIds)
                          ->where('organizacion_id', $this->selectedOrgId)
                          ->orWhere('usuario_id', $user->id);
                    });
                } else {
                    // Agricultor ve lo suyo (de la org o personal)
                    $baseQuery = Terreno::where('usuario_id', $user->id);
                }
            } else {
                $baseQuery = Terreno::where('usuario_id', $user->id);
            }
        } else {
            $baseQuery = Terreno::where('usuario_id', $user->id);
        }

        $baseQuery->withCount([
            'cultivos as plan_count' => fn($q) => $q->where('estado', 'Planificado'),
            'cultivos as sembr_count' => fn($q) => $q->where('estado', 'En crecimiento'),
            'cultivos as hist_count' => fn($q) => $q->whereIn('estado', ['Cosechado', 'Perdido']),
        ]);

        if ($this->search) {
            $baseQuery->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->filterCrop) {
            $baseQuery->whereHas('cultivos', function ($q) {
                $q->where('estado', 'En crecimiento')
                  ->whereHas('detalleCatalogo', function($sq) {
                      $sq->where('nombre', 'like', '%' . $this->filterCrop . '%');
                  });
            });
        }

        if ($this->filterArea) {
            if ($this->filterArea === '0-5') $baseQuery->where('hectareas', '<', 5);
            elseif ($this->filterArea === '5-10') $baseQuery->where('hectareas', '>=', 5)->where('hectareas', '<=', 10);
            elseif ($this->filterArea === '10+') $baseQuery->where('hectareas', '>', 10);
        }

        // Clonamos para los totales y mapa antes de paginar
        $allResultsQuery = clone $baseQuery;
        $terrenos = $baseQuery->paginate(12);

        $allResults = $allResultsQuery->with(['cultivos.detalleCatalogo'])->get();

        // --- Lógica del Line Chart de Terrenos ALQUILADOS ---
        $lineChartData = [
            'labels' => [],
            'values' => [],
            'unit' => 'ha'
        ];

        // Filtramos solo los terrenos alquilados del conjunto de resultados actual
        $rentedTerrains = $allResults->where('tipo_tenencia', 'alquilado')->whereNotNull('fecha_alquiler');

        if ($this->chartPeriod === 'mes') {
            // Para que coincida con la vista del usuario, generamos un rango de 12 meses
            // que incluya meses pasados y futuros si hay datos.
            $startMonth = Carbon::now()->subMonths(4);

            for ($i = 0; $i < 12; $i++) {
                $currentIterationDate = (clone $startMonth)->addMonths($i);
                $label = $currentIterationDate->translatedFormat('M Y');
                $lineChartData['labels'][] = $label;

                // Sumamos hectáreas cuya fecha_alquiler coincida con el mes y año
                $sum = $rentedTerrains->filter(function($t) use ($currentIterationDate) {
                    $f = Carbon::parse($t->fecha_alquiler);
                    return $f->format('Y-m') === $currentIterationDate->format('Y-m');
                })->sum('hectareas');

                $lineChartData['values'][] = (float)$sum;
            }
        } else {
            // Agrupación por Año (últimos 6 años)
            $startYear = Carbon::now()->subYears(3);
            for ($i = 0; $i < 6; $i++) {
                $currentIterationYear = (clone $startYear)->addYears($i);
                $yearStr = $currentIterationYear->format('Y');
                $lineChartData['labels'][] = $yearStr;

                $sum = $rentedTerrains->filter(function($t) use ($yearStr) {
                    return Carbon::parse($t->fecha_alquiler)->format('Y') === $yearStr;
                })->sum('hectareas');

                $lineChartData['values'][] = (float)$sum;
            }
        }

        $mapData = \App\Models\Terreno::where('estado', 1)
            ->get()
            ->map(function($t) use ($user) {
                $esMio = $t->usuario_id === $user->id;
                $isExpired = $t->fecha_vencimiento_alquiler && $t->fecha_vencimiento_alquiler->isPast();

                // Si es un terreno alquilado y venció
                if ($t->tipo_tenencia === 'alquilado' && $isExpired) {
                    if ($esMio) {
                        return [
                            'id' => $t->id,
                            'nombre' => $t->nombre,
                            'lat' => (float)$t->latitud,
                            'lng' => (float)$t->longitud,
                            'area' => $t->hectareas,
                            'color' => 'purple',
                            'type' => 'marker',
                            'label' => "Alquiler Vencido: {$t->nombre} (" . number_format($t->hectareas, 2) . " Ha)",
                            'poligono' => null,
                            'es_mio' => true
                        ];
                    }
                    return null;
                }

                $color = 'red';
                $label = number_format($t->hectareas, 2) . ' Ha';

                if ($esMio) {
                    if ($t->tipo_tenencia === 'propio') {
                        $color = 'green';
                        $label = "Terreno Propio: {$t->nombre} (" . number_format($t->hectareas, 2) . " Ha)";
                    } else {
                        $color = 'blue';
                        $label = "Terreno Alquilado: {$t->nombre} (" . number_format($t->hectareas, 2) . " Ha)";
                    }
                }

                return [
                    'id' => $t->id,
                    'nombre' => $t->nombre,
                    'lat' => (float)$t->latitud,
                    'lng' => (float)$t->longitud,
                    'area' => $t->hectareas,
                    'color' => $color,
                    'type' => 'polygon',
                    'label' => $label,
                    'poligono' => is_string($t->poligono) ? json_decode($t->poligono, true) : $t->poligono,
                    'es_mio' => $esMio
                ];
            })->filter()->values();

        return view('livewire.admin.terrenos-manager', [
            'terrenos' => $terrenos,
            'mapData' => $mapData,
            'lineChartData' => $lineChartData,
            'totalArea' => $allResults->sum('hectareas'),
            'totalCount' => $allResults->count(),
            'misOrganizaciones' => $misOrganizaciones
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCrop', 'filterArea', 'activeLandId']);
    }

    public $selectedLandForDetail = null;

    public function selectActiveLand($id)
    {
        $this->selectedLandForDetail = Terreno::with(['organizacion', 'responsable', 'cultivos.detalleCatalogo'])->findOrFail($id);
        $this->dispatch('open-modal', 'modal-land-details');
    }
}
