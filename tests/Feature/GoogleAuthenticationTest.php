<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.google.client_id', 'test-client-id');
        config()->set('services.google.client_secret', 'test-client-secret');
        config()->set('services.google.redirect', 'http://localhost:8000/auth/google/callback');
    }

    private function fakeGoogleUser(array $attributes): SocialiteUser
    {
        return (new SocialiteUser)->map($attributes);
    }

    public function test_login_page_offers_google_sign_in(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk dengan Google');
        $this->get(route('register'))->assertOk()->assertSee('Daftar dengan Google');
    }

    public function test_user_is_redirected_to_google(): void
    {
        Socialite::fake('google');

        $this->get(route('google.redirect'))->assertRedirect();
    }

    public function test_existing_google_user_logs_in_directly(): void
    {
        $user = User::factory()->active()->create(['google_id' => 'google-123']);

        Socialite::fake('google', $this->fakeGoogleUser([
            'id' => 'google-123',
            'name' => $user->name,
            'email' => $user->email,
        ]));

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_customer_is_linked_by_verified_email(): void
    {
        $user = User::factory()->active()->create(['email' => 'lama@example.com']);

        Socialite::fake('google', $this->fakeGoogleUser([
            'id' => 'google-999',
            'name' => 'Nama Google',
            'email' => 'lama@example.com',
        ]));

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertSame('google-999', $user->refresh()->google_id);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_account_cannot_use_google(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com']);

        Socialite::fake('google', $this->fakeGoogleUser([
            'id' => 'google-admin',
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]));

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull(User::query()->where('email', 'admin@example.com')->firstOrFail()->google_id);
    }

    public function test_new_google_user_must_accept_legal_terms(): void
    {
        Socialite::fake('google', $this->fakeGoogleUser([
            'id' => 'google-baru',
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
        ]));

        $this->get(route('google.callback'))->assertRedirect(route('google.confirm'));

        $this->get(route('google.confirm'))
            ->assertOk()
            ->assertSee('Pengguna Baru')
            ->assertSee('baru@example.com')
            ->assertSee('Syarat dan Ketentuan');

        $this->post(route('google.confirm.store'))
            ->assertSessionHasErrors('terms');

        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);

        $this->post(route('google.confirm.store'), ['terms' => '1'])
            ->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'baru@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $user->role);
        $this->assertSame(User::STATUS_ACTIVE, $user->status);
        $this->assertSame('google-baru', $user->google_id);
        $this->assertSame(config('legal.terms_version'), $user->terms_version);
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_callback_without_email_is_rejected(): void
    {
        Socialite::fake('google', $this->fakeGoogleUser(['id' => 'google-tanpa-email', 'name' => 'Tanpa Email']));

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_google_callback_with_unverified_email_is_rejected(): void
    {
        $fake = $this->fakeGoogleUser(['id' => 'google-belum-verif', 'name' => 'Belum Verif', 'email' => 'belum@example.com'])
            ->setRaw(['email_verified' => false]);
        Socialite::fake('google', $fake);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_expired_confirm_session_redirects_to_login(): void
    {
        $this->get(route('google.confirm'))
            ->assertRedirect(route('login'));

        $this->assertFalse(session()->has(GoogleAuthController::SESSION_KEY));
    }

    public function test_google_button_hidden_without_configuration(): void
    {
        config()->set('services.google.client_id');
        config()->set('services.google.client_secret');

        $this->get(route('login'))->assertOk()->assertDontSee('href="'.route('google.redirect').'"', false);
    }
}
