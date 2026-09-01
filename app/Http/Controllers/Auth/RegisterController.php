<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        User::query()->create([
            ...$request->safe()->only(['name', 'email', 'password']),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_PENDING,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Akun Anda menunggu persetujuan admin.');
    }
}
