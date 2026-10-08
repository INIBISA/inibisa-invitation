<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmGoogleRegistrationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public const SESSION_KEY = 'google_oauth_pending';

    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google belum tersedia. Silakan masuk dengan email dan kata sandi.']);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google belum tersedia. Silakan masuk dengan email dan kata sandi.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['email' => 'Tidak dapat terhubung ke Google. Silakan coba lagi.']);
        }

        $googleId = $googleUser->getId();
        $email = trim((string) $googleUser->getEmail());
        $raw = $googleUser->getRaw();

        if (! $googleId || $email === '') {
            return redirect()->route('login')->withErrors(['email' => 'Google tidak memberikan alamat email. Silakan daftar dengan email.']);
        }

        if (is_array($raw) && array_key_exists('email_verified', $raw) && $raw['email_verified'] === false) {
            return redirect()->route('login')->withErrors(['email' => 'Email Google Anda belum terverifikasi. Silakan verifikasi dulu di akun Google Anda.']);
        }

        $user = User::query()->where('google_id', $googleId)->first();

        if ($user) {
            if ($user->isAdmin()) {
                return redirect()->route('login')->withErrors(['email' => 'Akun administrator tidak dapat masuk melalui Google.']);
            }

            return $this->login($request, $user);
        }

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            if ($user->isAdmin()) {
                return redirect()->route('login')->withErrors(['email' => 'Akun administrator tidak dapat masuk melalui Google.']);
            }

            $user->update([
                'google_id' => $googleId,
                'google_avatar' => $googleUser->getAvatar(),
            ]);

            return $this->login($request, $user->refresh());
        }

        $request->session()->put(self::SESSION_KEY, [
            'google_id' => $googleId,
            'name' => $googleUser->getName() ?: explode('@', $email)[0],
            'email' => $email,
            'avatar' => $googleUser->getAvatar(),
        ]);

        return redirect()->route('google.confirm');
    }

    public function confirm(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (! is_array($pending)) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi pendaftaran Google kedaluwarsa. Silakan coba lagi.']);
        }

        return view('auth.google-confirm', ['pending' => $pending]);
    }

    public function storeConfirm(ConfirmGoogleRegistrationRequest $request): RedirectResponse
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (! is_array($pending)) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi pendaftaran Google kedaluwarsa. Silakan coba lagi.']);
        }

        $user = User::query()->where('google_id', $pending['google_id'])->first();

        if (! $user) {
            $user = User::query()->where('email', $pending['email'])->first();

            if ($user) {
                if ($user->isAdmin()) {
                    return redirect()->route('login')->withErrors(['email' => 'Akun administrator tidak dapat masuk melalui Google.']);
                }

                $user->update([
                    'google_id' => $pending['google_id'],
                    'google_avatar' => $pending['avatar'],
                ]);
                $user = $user->refresh();
            }
        }

        $user ??= User::query()->create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => Str::random(40),
            'google_id' => $pending['google_id'],
            'google_avatar' => $pending['avatar'],
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.terms_version'),
        ]);

        $request->session()->forget(self::SESSION_KEY);

        return $this->login($request, $user);
    }

    private function login(Request $request, User $user): RedirectResponse
    {
        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    private function isConfigured(): bool
    {
        return (bool) config('services.google.client_id')
            && (bool) config('services.google.client_secret')
            && (bool) config('services.google.redirect');
    }
}
