<?php

namespace App\Livewire\Ia;

use Livewire\Component;
use App\Models\AlertaSistema;
use App\Models\SugerenciaTarea;
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
        $this->cargarAlertas();
    }

    public function cargarAlertas()
    {
        $user = Auth::user();

        // Cargar alertas reales del sistema
        $this->alertasSistema = AlertaSistema::where('visible', 1)
            ->where(function($q) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Cargar sugerencias de tareas (que actúan como alertas de IA)
        $this->alertasIa = SugerenciaTarea::with('cultivo')
            ->where('agricultor_usuario_id', $user->id)
            ->where('estado', 'Pendiente')
            ->orderBy('fecha_sugerida', 'asc')
            ->get();

        // Si no hay alertas reales, cargamos unas simuladas para la vista
        if ($this->alertasIa->isEmpty()) {
            $this->alertasIa = collect([
                (object)[
                    'titulo' => 'Riesgo de Estrés Hídrico',
                    'descripcion' => 'La IA detectó una probabilidad del 85% de sequía en el Sector B para los próximos 5 días.',
                    'tipo' => 'critico',
                    'confianza' => 0.85
                ],
                (object)[
                    'titulo' => 'Detección Temprana de Plaga',
                    'descripcion' => 'Análisis de imágenes satelitales sugiere presencia de "Roya del Café" en el lote 4.',
                    'tipo' => 'advertencia',
                    'confianza' => 0.72
                ]
            ]);
        }
    }

    public function simularAnalisis()
    {
        $this->loading = true;
        sleep(2);
        $this->loading = false;

        $this->dispatch('notify', ['message' => 'Análisis de IA completado.', 'type' => 'success']);
        $this->cargarAlertas();
    }

    public function render()
    {
        return view('livewire.ia.alertas');
    }
}
