<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            ...$request->safe()->only(['name', 'email', 'password']),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.terms_version'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil. Selamat datang!');
    }
}
