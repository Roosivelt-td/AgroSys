<?php

namespace App\Livewire\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;

/**
 * LÓGICA (BACKEND) - Manejo de acciones de navegación y sesión.
 */
class Navigation extends Component
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
        return view('livewire.layout.navigation');
    }
}
