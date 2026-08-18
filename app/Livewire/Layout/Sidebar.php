<?php

namespace App\Livewire\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;

/**
 * LÓGICA (BACKEND) - Sidebar de Navegación.
 * Maneja las acciones globales del menú lateral.
 */
class Sidebar extends Component
{
    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
