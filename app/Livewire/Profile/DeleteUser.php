<?php

namespace App\Livewire\Profile;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUser extends Component
{
    public string $password = '';

    /**
     * Elimina la cuenta del usuario.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.profile.delete-user-form');
    }
}
