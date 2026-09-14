<?php

namespace App\Livewire\Admin;

use App\Models\Venta;
use App\Models\Cosecha;
use App\Models\Comprador;
use App\Models\MiembroOrganizacion;
use App\Models\ArchivoMultimedia;
use App\Services\AgroStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Ventas')]
class VentasManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedOrgId = null;

    // Propiedades del Formulario
    public $ventaId = null;
    public $cosecha_id = '';
    public $comprador_id = '';
    public $fecha_venta = '';
    public $cantidad_vendida_kg = '';
    public $precio_por_kg = '';
    public $costo_flete = 0;
    public $impuestos = 0;
    public $comprobante_tipo = 'boleta';
    public $comprobante_numero = '';
    public $observaciones = '';
    public $ventaPhoto;
    public $currentPhotoPath;

    // Búsqueda Dinámica
    public $queryCultivo = '';
    public $queryCosecha = '';
    public $queryComprador = '';
    public $showCultivos = false;
    public $showCosechas = false;
    public $showCompradores = false;

    public $cultivoSeleccionadoId = null;
    public $cultivoSeleccionadoLabel = '';
    public $cosechaSeleccionadaLabel = '';
    public $compradorSeleccionadoNombre = '';
    public $unidadMedidaCosecha = 'kg';
    public $unidad_venta = 'kg';

    // Info técnica del cultivo seleccionado
    public $cultivoInfo = [
        'variedad' => '',
        'area' => '',
        'fecha_cosecha' => '',
        'foto' => ''
    ];

    // Propiedades de Seguridad
    public $confirmingAction = false;
    public $actionToPerform = ''; // 'edit' o 'delete'
    public $ventaIdToPerform = null;
    public $adminPassword = '';

    // Propiedades para Reporte de Detalle
    public $selectedVentaReport = null;
    public $reportVentaData = [];

    // Propiedades para Registro Rápido de Comprador
    public $newCompNombre = '';
    public $newCompRucDni = '';
    public $newCompTelf = '';
    public $newCompEmail = '';
    public $newCompDir = '';

    public function mount()
    {
        $this->fecha_venta = date('Y-m-d');
        $membresia = MiembroOrganizacion::where('usuario_id', Auth::id())
            ->where('estado', 1)
            ->first();

        if ($membresia) {
            $this->selectedOrgId = $membresia->organizacion_id;
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->generateComprobanteNumero(); // Generar número inicial
        $this->dispatch('open-modal', 'modal-venta-manager');
    }

    public function resetForm()
    {
        $this->reset([
            'ventaId', 'cosecha_id', 'comprador_id', 'cantidad_vendida_kg',
            'precio_por_kg', 'costo_flete', 'impuestos', 'comprobante_tipo',
            'comprobante_numero', 'observaciones', 'queryCultivo', 'queryCosecha', 'queryComprador',
            'cultivoSeleccionadoId', 'cultivoSeleccionadoLabel', 'cosechaSeleccionadaLabel',
            'compradorSeleccionadoNombre', 'ventaPhoto', 'currentPhotoPath', 'unidadMedidaCosecha', 'unidad_venta'
        ]);
        $this->cultivoInfo = ['variedad' => '', 'area' => '', 'fecha_cosecha' => '', 'foto' => ''];
        $this->fecha_venta = date('Y-m-d');
    }

    public function selectCultivo($id, $label)
    {
        $this->cultivoSeleccionadoId = $id;
        $this->cultivoSeleccionadoLabel = $label;
        $this->queryCultivo = '';
        $this->showCultivos = false;

        $crop = \App\Models\Cultivo::with('detalleCatalogo')->find($id);
        if ($crop) {
            $this->cultivoInfo = [
                'variedad' => $crop->variedad ?: 'Genérica',
                'area' => $crop->area_destinada . ' HA',
                'fecha_cosecha' => $crop->fecha_cosecha_finalizada ? $crop->fecha_cosecha_finalizada->format('d/m/Y') : 'En proceso',
                'foto' => $crop->foto_path
            ];

            // Búsqueda de la última venta de este cultivo para sugerir el comprador
            $lastSale = Venta::whereHas('cosecha.labor', function($q) use ($id) {
                $q->where('cultivo_id', $id);
            })->with('comprador')->orderBy('fecha_venta', 'desc')->first();

            if ($lastSale) {
                $this->comprador_id = $lastSale->comprador_id;
                $this->compradorSeleccionadoNombre = $lastSale->comprador->nombre;
            }
        }

        // Reset cosecha al cambiar cultivo
        $this->cosecha_id = null;
        $this->cosechaSeleccionadaLabel = '';
        $this->unidadMedidaCosecha = 'kg';
    }

    public function clearCultivoSelection()
    {
        $this->reset(['cultivoSeleccionadoId', 'cultivoSeleccionadoLabel', 'cosecha_id', 'cosechaSeleccionadaLabel', 'queryCultivo', 'queryCosecha', 'unidad_venta', 'cantidad_vendida_kg', 'precio_por_kg']);
        $this->showCultivos = true;
    }

    public function selectCosecha($id, $label, $stock, $unidad = 'kg')
    {
        $this->cosecha_id = $id;
        $this->cosechaSeleccionadaLabel = $label;
        $this->unidadMedidaCosecha = $unidad;
        $this->unidad_venta = $unidad; // Sincronizamos la unidad de venta con la de cosecha por defecto
        $this->cantidad_vendida_kg = $stock; // Sugerimos vender todo lo disponible
        $this->showCosechas = false;
        $this->queryCosecha = '';
        $this->generateComprobanteNumero();
    }

    public function updatedComprobanteTipo()
    {
        $this->generateComprobanteNumero();
    }

    public function generateComprobanteNumero()
    {
        $prefix = match($this->comprobante_tipo) {
            'factura' => 'F001',
            'boleta' => 'B001',
            default => 'T001'
        };

        $lastVenta = Venta::where('comprobante_tipo', $this->comprobante_tipo)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastVenta && preg_match('/-(\d+)$/', $lastVenta->comprobante_numero, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $this->comprobante_numero = $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function selectComprador($id, $nombre)
    {
        $this->comprador_id = $id;
        $this->compradorSeleccionadoNombre = $nombre;
        $this->showCompradores = false;
        $this->queryComprador = '';
    }

    public function openAddComprador()
    {
        $this->reset(['newCompNombre', 'newCompRucDni', 'newCompTelf', 'newCompEmail', 'newCompDir']);
        $this->dispatch('open-modal', 'modal-add-comprador');
    }

    public function saveQuickComprador()
    {
        $this->validate([
            'newCompNombre' => 'required|string|max:200',
            'newCompRucDni' => 'nullable|string|max:20',
        ]);

        $comprador = \App\Models\Comprador::create([
            'nombre' => strtoupper($this->newCompNombre),
            'ruc_dni' => $this->newCompRucDni,
            'telefono' => $this->newCompTelf,
            'email' => $this->newCompEmail,
            'direccion' => $this->newCompDir,
        ]);

        $this->comprador_id = $comprador->id;
        $this->compradorSeleccionadoNombre = $comprador->nombre;

        $this->dispatch('close-modal', 'modal-add-comprador');
        session()->flash('status', 'Comprador registrado y seleccionado.');
    }

    public function save()
    {
        $this->validate([
            'cosecha_id' => 'required',
            'comprador_id' => 'required',
            'fecha_venta' => 'required|date',
            'cantidad_vendida_kg' => 'required|numeric|min:0.01',
            'precio_por_kg' => 'required|numeric|min:0.01',
            'comprobante_tipo' => 'required',
            'ventaPhoto' => 'nullable|image|max:5120'
        ]);

        $user = Auth::user();
        $path = $this->currentPhotoPath;

        if ($this->ventaPhoto) {
            $fileData = AgroStorageService::storeUserFile($this->ventaPhoto, $user, 'comercializacion', $this->selectedOrgId);
            $path = $fileData['ruta_completa'];

            ArchivoMultimedia::create([
                'usuario_id' => $user->id,
                'organizacion_id' => $this->selectedOrgId,
                'nombre_original' => $fileData['nombre_original'],
                'nombre_archivo_unique' => $fileData['nombre_archivo_unique'],
                'ruta_completa' => $path,
                'tipo_mime' => $fileData['tipo_mime'],
                'tamano_bytes' => $fileData['tamano_bytes']
            ]);
        }

        $data = [
            'cosecha_id' => $this->cosecha_id,
            'comprador_id' => $this->comprador_id,
            'fecha_venta' => $this->fecha_venta,
            'cantidad_vendida_kg' => $this->cantidad_vendida_kg,
            'precio_por_kg' => $this->precio_por_kg,
            'costo_flete' => $this->costo_flete ?: 0,
            'impuestos' => $this->impuestos ?: 0,
            'comprobante_tipo' => $this->comprobante_tipo,
            'comprobante_numero' => $this->comprobante_numero,
            'foto_path' => $path
        ];

        DB::transaction(function() use ($data) {
            if ($this->ventaId) {
                $v = Venta::find($this->ventaId);
                $v->update($data);
            } else {
                Venta::create($data);
            }

            // --- SINCRONIZACIÓN DE REALIDAD (Sustituye estimaciones por datos reales) ---
            $cosecha = Cosecha::find($this->cosecha_id);
            if ($cosecha) {
                // 1. La unidad de la cosecha ahora es la unidad de venta real
                $cosecha->unidad_medida = $this->unidad_venta;

                // 2. La cantidad total cosechada se ajusta a la sumatoria de ventas reales
                $totalRealVendido = Venta::where('cosecha_id', $this->cosecha_id)->sum('cantidad_vendida_kg');
                $cosecha->cantidad_kg = $totalRealVendido;
                $cosecha->save();

                // 3. Sincronizar con el Cultivo (Campaña)
                $cultivo = $cosecha->labor->cultivo;
                if ($cultivo) {
                    // Actualizar el rendimiento estadístico por el RENDIMIENTO REAL
                    // Calculamos: (Total KG vendidos / 1000) / Hectáreas = TN/HA Reales
                    $totalTn = ($totalRealVendido > 0 && $this->unidad_venta === 'tn') ? $totalRealVendido : ($totalRealVendido / 1000);

                    // Si la unidad de venta no era TN ni KG (ej: sacos), intentamos una conversión lógica o mantenemos el valor
                    if (!in_array($this->unidad_venta, ['kg', 'tn'])) {
                        // Si son sacos/und, asumimos que el peso ya está en el campo cantidad_vendida_kg si el usuario lo ingresó así,
                        // o simplemente actualizamos el rendimiento basado en la magnitud vendida.
                        $cultivo->rendimiento_esperado_tn_ha = $totalRealVendido / max(0.01, $cultivo->area_destinada);
                    } else {
                        $cultivo->rendimiento_esperado_tn_ha = $totalTn / max(0.01, $cultivo->area_destinada);
                    }

                    $cultivo->estado = 'Cosechado';
                    $cultivo->save();
                }
            }
        });

        $this->dispatch('close-modal', 'modal-venta-manager');
        $this->resetForm();
        session()->flash('status', 'Transacción procesada y datos de cosecha sincronizados.');
    }

    // LÓGICA DE SEGURIDAD PARA EDICIÓN Y ELIMINACIÓN
    public function requestSecureAction($id, $action)
    {
        $this->ventaIdToPerform = $id;
        $this->actionToPerform = $action;
        $this->adminPassword = '';
        $this->dispatch('open-modal', 'modal-confirm-secure-action');
    }

    public function verifyPasswordAndPerform()
    {
        if (!\Illuminate\Support\Facades\Hash::check($this->adminPassword, Auth::user()->password)) {
            $this->addError('adminPassword', 'Contraseña incorrecta. Acción denegada.');
            return;
        }

        if ($this->actionToPerform === 'edit') {
            $this->startEditing();
        } elseif ($this->actionToPerform === 'delete') {
            $this->deleteVenta();
        }

        $this->dispatch('close-modal', 'modal-confirm-secure-action');
    }

    protected function startEditing()
    {
        $v = Venta::with(['cosecha.labor.cultivo', 'comprador'])->findOrFail($this->ventaIdToPerform);
        $this->ventaId = $v->id;
        $this->cosecha_id = $v->cosecha_id;
        $this->comprador_id = $v->comprador_id;
        $this->fecha_venta = $v->fecha_venta->format('Y-m-d');
        $this->cantidad_vendida_kg = $v->cantidad_vendida_kg;
        $this->precio_por_kg = $v->precio_por_kg;
        $this->costo_flete = $v->costo_flete;
        $this->impuestos = $v->impuestos;
        $this->comprobante_tipo = $v->comprobante_tipo;
        $this->comprobante_numero = $v->comprobante_numero;
        $this->currentPhotoPath = $v->foto_path;

        // Labels y búsquedas
        $crop = $v->cosecha->labor->cultivo;
        $this->cultivoSeleccionadoId = $crop->id;
        $this->cultivoSeleccionadoLabel = strtoupper($crop->detalleCatalogo->nombre) . " " . strtoupper($crop->variedad ?: 'GENERICA');
        $this->cosechaSeleccionadaLabel = "CALIDAD: " . strtoupper($v->cosecha->calidad) . " - " . $v->cosecha->fecha_cosecha->format('d/m/Y');
        $this->compradorSeleccionadoNombre = $v->comprador->nombre;
        $this->unidadMedidaCosecha = $v->cosecha->unidad_medida;
        $this->unidad_venta = $v->cosecha->unidad_medida; // Recuperamos la unidad para el formulario de edición

        $this->cultivoInfo = [
            'variedad' => $crop->variedad ?: 'Genérica',
            'area' => $crop->area_destinada . ' HA',
            'fecha_cosecha' => $crop->fecha_cosecha_finalizada ? $crop->fecha_cosecha_finalizada->format('d/m/Y') : 'En proceso',
            'foto' => $crop->foto_path
        ];

        $this->dispatch('open-modal', 'modal-venta-manager');
    }

    protected function deleteVenta()
    {
        $v = Venta::findOrFail($this->ventaIdToPerform);
        $v->delete();
        session()->flash('status', 'Transacción eliminada permanentemente.');
    }

    public function showVentaReport($id)
    {
        $venta = Venta::with(['cosecha.labor.cultivo.labores.cosechas.ventas.comprador', 'comprador', 'cosecha.labor.cultivo.detalleCatalogo'])->findOrFail($id);
        $this->selectedVentaReport = $venta;

        $cultivo = $venta->cosecha->labor->cultivo;

        // Inversión Global del Cultivo (Suma de todas las labores)
        $inversionGlobal = $cultivo->labores->sum('costo_total');

        // Todas las ventas de este cultivo
        $todasLasVentasCultivo = Venta::whereHas('cosecha.labor', fn($q) => $q->where('cultivo_id', $cultivo->id))
            ->with(['comprador', 'cosecha'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $ventasTotalesCultivo = $todasLasVentasCultivo->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg);
        $fletesTotalesCultivo = $todasLasVentasCultivo->sum('costo_flete');

        // Agrupación de ventas por Comprador y Fecha (para el desglose del informe)
        $ventasAgrupadas = $todasLasVentasCultivo->groupBy(function($item) {
            return $item->comprador_id . '_' . $item->fecha_venta->format('Y-m-d');
        })->map(function($group) {
            return [
                'comprador' => $group->first()->comprador->nombre,
                'fecha' => $group->first()->fecha_venta->format('d/m/Y'),
                'monto_total' => $group->sum(fn($v) => $v->cantidad_vendida_kg * $v->precio_por_kg),
                'flete_total' => $group->sum('costo_flete'),
                'foto' => $group->first()->foto_path,
                'detalles' => $group->map(fn($v) => [
                    'id' => $v->id,
                    'calidad' => $v->cosecha->calidad,
                    'cantidad' => $v->cantidad_vendida_kg,
                    'unidad' => $v->cosecha->unidad_medida,
                    'precio' => $v->precio_por_kg,
                    'flete' => $v->costo_flete
                ])
            ];
        })->values()->toArray();

        $this->reportVentaData = [
            'inversion_global' => $inversionGlobal,
            'monto_bruto_campana' => $ventasTotalesCultivo,
            'flete_total_campana' => $fletesTotalesCultivo,
            'ganancia_neta_estable_campana' => $ventasTotalesCultivo - $fletesTotalesCultivo,
            'ganancia_total_real_campana' => $ventasTotalesCultivo - $inversionGlobal - $fletesTotalesCultivo,
            'ventas_agrupadas' => $ventasAgrupadas,
            'unidad' => $venta->cosecha->unidad_medida,
            'calidad' => $venta->cosecha->calidad,
            'variedad' => $cultivo->variedad ?: 'Genérica',
            'area' => $cultivo->area_destinada
        ];

        $this->dispatch('open-modal', 'modal-venta-report');
    }

    public function render()
    {
        // 1. Obtener los IDs de los cultivos que tienen ventas (considerando organización/usuario)
        $orgFilter = function($q) {
            if ($this->selectedOrgId) {
                $q->where('organizacion_id', $this->selectedOrgId)
                  ->orWhere('usuario_id', Auth::id());
            } else {
                $q->where('usuario_id', Auth::id());
            }
        };

        // 2. Consulta agrupada por Cultivo
        // Queremos mostrar UNA SOLA TARJETA por cultivo vendido
        $query = Venta::query()
            ->whereHas('cosecha.labor.cultivo.terreno', $orgFilter)
            ->with(['cosecha.labor.cultivo.detalleCatalogo', 'cosecha.labor.cultivo.terreno', 'cosecha.labor.cultivo.labores.cosechas.ventas', 'comprador'])
            ->select('ventas.*')
            ->whereIn('ventas.id', function($q) {
                $q->select(DB::raw('MIN(v2.id)'))
                  ->from('ventas as v2')
                  ->join('cosechas as c2', 'v2.cosecha_id', '=', 'c2.id')
                  ->join('labores as l2', 'c2.labor_id', '=', 'l2.id')
                  ->groupBy('l2.cultivo_id'); // Agrupamos definitivamente por CULTIVO
            });

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('comprador', fn($sq) => $sq->where('nombre', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('cosecha.labor.cultivo.detalleCatalogo', fn($sq) => $sq->where('nombre', 'like', '%' . $this->search . '%'));
            });
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->paginate(12);

        // Transformamos la colección para que cada tarjeta represente el RESUMEN DEL CULTIVO
        $ventas->getCollection()->transform(function($v) {
            $cultivo = $v->cosecha->labor->cultivo;

            // Cálculos Consolidados
            $v->total_inversion_cultivo = $cultivo->labores->sum('costo_total');
            $v->total_fletes_cultivo = $cultivo->labores->flatMap->cosechas->flatMap->ventas->sum('costo_flete');
            $v->total_venta_bruta_cultivo = $cultivo->labores->flatMap->cosechas->flatMap->ventas->sum(fn($sale) => $sale->cantidad_vendida_kg * $sale->precio_por_kg);

            // Cantidad de ventas (agrupadas por cliente y fecha)
            $v->conteo_ventas_reales = $cultivo->labores->flatMap->cosechas->flatMap->ventas
                ->groupBy(fn($sale) => $sale->comprador_id . '_' . $sale->fecha_venta->format('Y-m-d'))
                ->count();

            // Primera cosecha y venta para mostrar en el HUD de la tarjeta
            $v->primera_siembra_date = $cultivo->fecha_siembra;
            $v->primera_cosecha_date = $cultivo->labores->flatMap->cosechas->min('fecha_cosecha');
            $v->primera_venta_date = $cultivo->labores->flatMap->cosechas->flatMap->ventas->min('fecha_venta');

            return $v;
        });

        // Búsqueda Dinámica de Cultivos para el Modal
        $resultsCultivos = \App\Models\Cultivo::with(['detalleCatalogo', 'labores.cosechas.ventas'])
            ->whereIn('estado', ['Cosechado', 'En crecimiento'])
            ->whereHas('terreno', $orgFilter)
            ->when($this->queryCultivo, function($q) {
                $q->where(function($sq) {
                    $sq->whereHas('detalleCatalogo', fn($ssq) => $ssq->where('nombre', 'like', '%' . $this->queryCultivo . '%'))
                      ->orWhere('variedad', 'like', '%' . $this->queryCultivo . '%');
                });
            })
            ->get()->filter(function($c) {
                return $c->labores->flatMap->cosechas->contains(function($cos) {
                    $vendido = $cos->ventas->sum('cantidad_vendida_kg');
                    return ($cos->cantidad_kg - $vendido) > 0.01;
                });
            })->map(function($c) {
                $c->display_name = strtoupper($c->detalleCatalogo->nombre) . " " . strtoupper($c->variedad ?: 'GENERICA');
                $c->display_harvest_date = $c->fecha_cosecha_finalizada ? $c->fecha_cosecha_finalizada->format('d/m/Y') : 'EN PROCESO';
                return $c;
            })->take(10);

        $resultsCosechas = [];
        if ($this->cultivoSeleccionadoId) {
            $resultsCosechas = Cosecha::with('ventas')
                ->whereHas('labor', fn($q) => $q->where('cultivo_id', $this->cultivoSeleccionadoId))
                ->get()
                ->filter(fn($c) => ($c->cantidad_kg - $c->ventas->sum('cantidad_vendida_kg')) > 0.01)
                ->map(function($c) {
                    $c->display_label = "CALIDAD: " . strtoupper($c->calidad) . " - " . $c->fecha_cosecha->format('d/m/Y');
                    $c->available_stock = $c->cantidad_kg - $c->ventas->sum('cantidad_vendida_kg');
                    return $c;
                });
        }

        $resultsCompradores = [];
        if (strlen($this->queryComprador) > 0 || $this->showCompradores) {
            $resultsCompradores = \App\Models\Comprador::where('nombre', 'like', '%' . $this->queryComprador . '%')
                ->take(5)->get();
        }

        return view('livewire.admin.ventas-manager', [
            'ventas' => $ventas,
            'resultsCultivos' => $resultsCultivos,
            'resultsCosechas' => $resultsCosechas,
            'resultsCompradores' => $resultsCompradores,
            'hasCosechas' => Cosecha::whereHas('labor.cultivo.terreno', $orgFilter)->exists()
        ]);
    }
}
