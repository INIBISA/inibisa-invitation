<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authentication_pages_render_premium_forms(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Welcome Back')
            ->assertSee('Premium Templates')
            ->assertSee('data-password-toggle', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('Admin approval required');
    }

    public function test_registration_creates_pending_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'Customer Baru',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);
    }

    public function test_pending_customer_cannot_login(): void
    {
        User::factory()->create(['email' => 'pending@example.com']);

        $response = $this->post('/login', ['email' => 'pending@example.com', 'password' => 'password']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_active_customer_logs_in_to_dashboard_and_logs_out(): void
    {
        User::factory()->active()->create(['email' => 'active@example.com']);

        $response = $this->post('/login', ['email' => 'active@example.com', 'password' => 'password']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
