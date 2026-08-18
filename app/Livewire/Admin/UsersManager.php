<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Rol;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UsersManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUser = null;
    public $confirmingUserDeletion = false;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) return; // No auto-desactivarse

        $user->is_activo = !$user->is_activo;
        $user->save();

        session()->flash('status', 'Estado del usuario actualizado.');
    }

    public function render()
    {
        $users = User::with(['rol', 'membresias.organizacion'])
            ->where(function($query) {
                $query->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.users-manager', [
            'users' => $users
        ]);
    }
}
