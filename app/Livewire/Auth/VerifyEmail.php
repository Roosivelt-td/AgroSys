<?php

namespace App\Livewire\Auth;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class VerifyEmail extends Component
{
    /**
     * Envía un nuevo correo de verificación.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Cierra la sesión.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.verify-email')->layout('layouts.guest');
    }
}
