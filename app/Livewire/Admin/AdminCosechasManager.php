<?php

namespace App\Livewire\Admin;

use App\Models\Cosecha;
use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Producción Global - AgroSys')]
class AdminCosechasManager extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $cosechas = Cosecha::with(['labor.cultivo.detalleCatalogo', 'labor.cultivo.terreno.organizacion'])
            ->whereHas('labor.cultivo.detalleCatalogo', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orderBy('fecha_cosecha', 'desc')
            ->paginate(15);

        return view('livewire.admin.admin-cosechas-manager', [
            'cosechas' => $cosechas
        ]);
    }
}
