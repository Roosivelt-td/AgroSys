<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
{
    public LoginForm $form;

    /**
     * Maneja la autenticación.
     */
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
