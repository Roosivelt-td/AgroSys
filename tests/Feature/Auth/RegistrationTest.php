<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Authentication;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeLivewire(Authentication::class);
    }

    public function test_new_users_can_register(): void
    {
        Http::fake([
            '*' => Http::response(['valid' => true], 200),
        ]);

        Livewire::test(Authentication::class)
            ->set('mode', 'register')
            ->set('nombres', 'Test')
            ->set('apellidos', 'User')
            ->set('dni', '12345678')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }
}
