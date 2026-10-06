<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Authentication;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeLivewire(Authentication::class);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $rolAgri = Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);
        $user = User::create([
            'nombres' => 'Juan', 'apellidos' => 'Pérez', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'auth.test.' . rand(100, 999) . '@agrosys.com', 'password' => 'password', 'rol_id' => $rolAgri->id, 'is_activo' => true
        ]);

        Livewire::test(Authentication::class)
            ->set('loginEmail', $user->email)
            ->set('loginPassword', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $rolAgri = Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);
        $user = User::create([
            'nombres' => 'Juan', 'apellidos' => 'Pérez', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'auth.test.' . rand(100, 999) . '@agrosys.com', 'password' => 'password', 'rol_id' => $rolAgri->id, 'is_activo' => true
        ]);

        Livewire::test(Authentication::class)
            ->set('loginEmail', $user->email)
            ->set('loginPassword', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['loginEmail'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $rolAgri = Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);
        $user = User::create([
            'nombres' => 'Juan', 'apellidos' => 'Pérez', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'auth.test.' . rand(100, 999) . '@agrosys.com', 'password' => 'password', 'rol_id' => $rolAgri->id, 'is_activo' => true
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
    }

    public function test_users_can_logout(): void
    {
        $rolAgri = Rol::firstOrCreate(['id' => 2], ['nombre' => 'Agricultor']);
        $user = User::create([
            'nombres' => 'Juan', 'apellidos' => 'Pérez', 'dni' => (string)rand(10000000, 99999999),
            'email' => 'auth.test.' . rand(100, 999) . '@agrosys.com', 'password' => 'password', 'rol_id' => $rolAgri->id, 'is_activo' => true
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Layout\Navigation::class)
            ->call('logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
