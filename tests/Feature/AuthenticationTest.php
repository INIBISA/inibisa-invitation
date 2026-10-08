<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authentication_pages_render_indonesian_forms(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Selamat Datang Kembali')
            ->assertSee('Awal kisah yang indah.')
            ->assertSee('data-password-toggle', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat Akun')
            ->assertSee('Syarat dan Ketentuan')
            ->assertSee('Kebijakan Privasi');
    }

    public function test_registration_creates_active_customer_with_terms_consent(): void
    {
        $response = $this->post('/register', [
            'name' => 'Customer Baru',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'terms_version' => config('legal.terms_version'),
        ]);
        $this->assertNotNull(User::query()->where('email', 'customer@example.com')->firstOrFail()->terms_accepted_at);
    }

    public function test_registration_accepts_simple_six_character_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Customer Mudah',
            'email' => 'mudah@example.com',
            'password' => 'abcdef',
            'password_confirmation' => 'abcdef',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'mudah@example.com']);
    }

    public function test_registration_rejects_short_password_with_indonesian_message(): void
    {
        $response = $this->from(route('register'))->post('/register', [
            'name' => 'Customer Baru',
            'email' => 'pendek@example.com',
            'password' => 'abc',
            'password_confirmation' => 'abc',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertStringContainsString('minimal 6 karakter', session('errors')->first('password'));
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $response = $this->from(route('register'))->post('/register', [
            'name' => 'Customer Baru',
            'email' => 'tanpa-setuju@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'tanpa-setuju@example.com']);
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
