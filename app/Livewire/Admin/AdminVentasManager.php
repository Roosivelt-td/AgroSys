<?php

namespace App\Livewire\Admin;

use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Comercialización Global - AgroSys')]
class AdminVentasManager extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $ventas = Venta::with(['cosecha.labor.cultivo.detalleCatalogo', 'cosecha.labor.cultivo.terreno.organizacion', 'comprador'])
            ->whereHas('cosecha.labor.cultivo.detalleCatalogo', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('comprador', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orderBy('fecha_venta', 'desc')
            ->paginate(15);

        return view('livewire.admin.admin-ventas-manager', [
            'ventas' => $ventas
        ]);
    }
}
