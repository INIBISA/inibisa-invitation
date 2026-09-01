<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings');
    }

    public function update(UpdateAdminSettingsRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $attributes['password'] = $request->string('password')->toString();
        }

        $request->user()->update($attributes);

        return back()->with('success', 'Admin settings updated successfully.');
    }
}
