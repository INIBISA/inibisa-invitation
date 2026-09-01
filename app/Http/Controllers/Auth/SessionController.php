<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau password tidak sesuai.']);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user?->status !== User::STATUS_ACTIVE) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $user?->status === User::STATUS_REJECTED
                ? 'Pendaftaran Anda ditolak. Hubungi admin untuk informasi lebih lanjut.'
                : 'Akun Anda masih menunggu persetujuan admin.';

            return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
