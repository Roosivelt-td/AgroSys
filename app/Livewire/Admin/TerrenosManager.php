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

#[Layout('layouts.app')]
class TerrenosManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterCrop = '';
    public $filterArea = '';
    public $selectedOrgId = null;
    public $chartPeriod = 'mes'; // mes o año

    // Campos de la Base de Datos
    public $landId = null;
    public $landName = '';
    public $landLocation = '';
    public $landDirRef = '';
    public $landLat = '';
    public $landLng = '';
    public $landArea = '';
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
            'landId', 'landName', 'landLocation', 'landDirRef', 'landLat', 'landLng',
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
            'landPhoto' => 'nullable|image|max:5120',
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
            'hectareas' => $this->landArea,
            'tipo_tenencia' => $this->landTenure,
            'costo_alquiler_anual' => ($this->landTenure === 'alquilado') ? $this->landRentCost : 0,
            'alquiler_modalidad' => ($this->landTenure === 'alquilado') ? $this->landRentMod : 'global',
            'alquiler_periodo' => ($this->landTenure === 'alquilado') ? $this->landRentPeriod : 'fecha',
            'fecha_alquiler' => ($this->landTenure === 'alquilado' && $this->landRentStart) ? strtotime($this->landRentStart) : null,
            'fecha_vencimiento_alquiler' => ($this->landTenure === 'alquilado' && $this->landRentEnd) ? strtotime($this->landRentEnd) : null,
            'calidad_suelo' => $this->landSoil,
            'fuente_agua' => $this->landWater,
            'estado_terreno' => $this->landStatus,
            'foto_path' => $photoPath,
            'usuario_id' => $user->id,
            'organizacion_id' => $this->selectedOrgId, // Ahora puede ser NULL
            'estado' => 1
        ];

        if ($this->landId) {
            Terreno::find($this->landId)->update($data);
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
        $this->landArea = $t->hectareas;
        $this->landTenure = $t->tipo_tenencia;
        $this->landRentCost = $t->costo_alquiler_anual;
        $this->landRentMod = $t->alquiler_modalidad ?? 'global';
        $this->landRentPeriod = $t->alquiler_periodo ?? 'fecha';
        $this->landRentStart = $t->fecha_alquiler ? date('Y-m-d', $t->fecha_alquiler) : '';
        $this->landRentEnd = $t->fecha_vencimiento_alquiler ? date('Y-m-d', $t->fecha_vencimiento_alquiler) : '';
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

        // Lógica de visibilidad por Rol y Organización
        if ($this->selectedOrgId) {
            $miembro = $user->membresias()->where('organizacion_id', $this->selectedOrgId)->where('estado', 1)->first();

            if ($user->rol_id === 1) {
                // Super Admin ve todo de la organización seleccionada
                $baseQuery = Terreno::where('organizacion_id', $this->selectedOrgId);
            } elseif ($miembro) {
                $esAdmin = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Administrador'))->where('estado', 1)->exists();
                $esSupervisor = $miembro->roles()->whereHas('rolDetalle', fn($q) => $q->where('nombre', 'Supervisor'))->where('estado', 1)->exists();

                if ($esAdmin) {
                    $baseQuery = Terreno::where('organizacion_id', $this->selectedOrgId);
                } elseif ($esSupervisor) {
                    $assignedIds = $user->getIdsAgricultoresAsignados($this->selectedOrgId);
                    $allowedUserIds = array_merge($allowedUserIds, $assignedIds);
                    $baseQuery = Terreno::whereIn('usuario_id', $allowedUserIds)->where('organizacion_id', $this->selectedOrgId);
                } else {
                    $baseQuery = Terreno::where('usuario_id', $user->id)->where('organizacion_id', $this->selectedOrgId);
                }
            } else {
                $baseQuery = Terreno::where('usuario_id', $user->id);
            }
        } else {
            $baseQuery = Terreno::where('usuario_id', $user->id);
        }

        $baseQuery->with(['cultivos' => function($q) {
            $q->where('estado', 'En crecimiento')->with('detalleCatalogo');
        }]);

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

        // --- Lógica del Line Chart de Terrenos ALQUILADOS (agrosys_terrenos_trend_v1) ---
        $lineChartData = [
            'labels' => [],
            'values' => [],
            'unit' => 'ha'
        ];

        // Filtramos solo los terrenos alquilados para la tendencia
        $rentedTerrains = $allResults->where('tipo_tenencia', 'alquilado')->whereNotNull('fecha_alquiler');

        if ($this->chartPeriod === 'mes') {
            // Generar labels para los últimos 12 meses
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $label = $date->format('M Y');
                $lineChartData['labels'][] = $label;

                // Sumamos hectáreas cuya fecha_alquiler caiga en este mes/año
                $sum = $rentedTerrains->filter(function($t) use ($date) {
                    $d = Carbon::createFromTimestamp($t->fecha_alquiler);
                    return $d->format('M Y') === $date->format('M Y');
                })->sum('hectareas');

                $lineChartData['values'][] = (float)$sum;
            }
        } else {
            // Generar labels para los últimos 6 años
            for ($i = 5; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i)->format('Y');
                $lineChartData['labels'][] = $year;

                $sum = $rentedTerrains->filter(function($t) use ($year) {
                    return Carbon::createFromTimestamp($t->fecha_alquiler)->format('Y') === $year;
                })->sum('hectareas');

                $lineChartData['values'][] = (float)$sum;
            }
        }

        $mapData = $allResults->map(function($t) {
            return [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'lat' => (float)$t->latitud,
                'lng' => (float)$t->longitud,
                'area' => $t->hectareas,
                'suelo' => $t->calidad_suelo,
                'cultivo' => $t->cultivos->where('estado', 'En crecimiento')->first()?->detalleCatalogo->nombre ?? 'Sin cultivo'
            ];
        });

        return view('livewire.admin.terrenos-manager', [
            'terrenos' => $terrenos,
            'mapData' => $mapData,
            'lineChartData' => $lineChartData,
            'totalArea' => $allResults->sum('hectareas'),
            'totalCount' => $allResults->count()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCrop', 'filterArea']);
    }
}
