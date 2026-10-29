<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $templateId = $request->has('template') ? $request->integer('template') : ($request->session()->has('errors') ? $request->session()->get('selected_template_id') : null);
        $selectedTemplate = Template::query()->where('is_active', true)->find($templateId);

        if ($selectedTemplate) {
            $request->session()->put('selected_template_id', $selectedTemplate->id);
        } else {
            $request->session()->forget('selected_template_id');
        }

        return view('auth.register', compact('selectedTemplate'));
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

        $selectedTemplate = Template::query()->where('is_active', true)->find($request->session()->pull('selected_template_id'));

        return redirect()->route($selectedTemplate ? 'payments.create' : 'dashboard', $selectedTemplate ? ['template' => $selectedTemplate] : [])
            ->with('success', 'Pendaftaran berhasil. Selamat datang!');
    }
}
