<?php

namespace App\Livewire\Ia;

use Livewire\Component;
use App\Models\AlertaSistema;
use App\Models\Cultivo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Alertas IA')]
class Alertas extends Component
{
    public $loading = false;
    public $alertasIa = [];
    public $alertasSistema = [];

    public function mount()
    {
        $this->cargarAlertasIa();
    }

    public function cargarAlertasIa()
    {
        $user = Auth::user();

        // 1. Cargar alertas del sistema
        $this->alertasSistema = AlertaSistema::where('visible', 1)
            ->where(function($q) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Recomendaciones inteligentes y diagnósticos de IA
        $cultivosUsuario = Cultivo::whereHas('terreno', function($q) use ($user) {
            $q->where('usuario_id', $user->id);
        })->with(['detalleCatalogo', 'terreno'])->get();

        $this->alertasIa = collect([
            (object)[
                'titulo' => 'Riesgo de Estrés Hídrico por Altas Temperaturas',
                'descripcion' => 'El modelo de IA predice una probabilidad del 88% de sequía en los próximos 5 días. Se sugiere programar un riego por goteo preventivo.',
                'tipo' => 'critico',
                'confianza' => 0.88,
                'lote' => $cultivosUsuario->first()?->nombre_lote ?? 'Lote Principal'
            ],
            (object)[
                'titulo' => 'Detección Temprana de Plaga (Análisis Satelital)',
                'descripcion' => 'Índices NDVI indican variaciones en la clorofila de la parcela. Posible presencia de Roya o Mildiú.',
                'tipo' => 'advertencia',
                'confianza' => 0.76,
                'lote' => $cultivosUsuario->skip(1)->first()?->nombre_lote ?? 'Lote Secundario'
            ],
            (object)[
                'titulo' => 'Ventana Óptima de Aplicación Foliar',
                'descripcion' => 'Condiciones de viento (< 10 km/h) y humedad (65%) favorables para fumigación durante las primeras horas de la mañana.',
                'tipo' => 'informativo',
                'confianza' => 0.92,
                'lote' => 'General Finca'
            ]
        ]);
    }

    public function simularAnalisis()
    {
        $this->loading = true;
        sleep(1);
        $this->loading = false;

        $this->dispatch('notify', ['message' => 'Escaneo de IA completado.', 'type' => 'success']);
        $this->cargarAlertasIa();
    }

    public function render()
    {
        return view('livewire.ia.alertas');
    }
}
