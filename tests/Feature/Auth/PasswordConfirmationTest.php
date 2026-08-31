<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\ConfirmPassword;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response
            ->assertOk()
            ->assertSeeLivewire(ConfirmPassword::class);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ConfirmPassword::class)
            ->set('password', 'password')
            ->call('confirmPassword')
            ->assertRedirect(route('dashboard', absolute: false))
            ->assertHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ConfirmPassword::class)
            ->set('password', 'wrong-password')
            ->call('confirmPassword')
            ->assertNoRedirect()
            ->assertHasErrors('password');
    }
}
