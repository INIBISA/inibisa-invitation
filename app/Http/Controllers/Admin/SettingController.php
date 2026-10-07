<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $manualTransfer = Setting::query()->where('key', 'manual_transfer')->value('value');
        $activePaymentMethod = Setting::activePaymentMethod();
        $isMidtransReady = (bool) config('payments.midtrans.server_key') && (bool) config('payments.midtrans.client_key');

        return view('admin.settings', compact('manualTransfer', 'activePaymentMethod', 'isMidtransReady'));
    }

    public function update(UpdateAdminSettingsRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $attributes['password'] = $request->string('password')->toString();
        }

        $request->user()->update($attributes);
        Setting::query()->updateOrCreate(['key' => 'manual_transfer'], ['value' => $request->string('manual_transfer')->trim()->toString() ?: null]);
        Setting::query()->updateOrCreate(['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD], ['value' => $request->string('active_payment_method')->toString()]);

        return back()->with('success', 'Pengaturan admin berhasil diperbarui.');
    }
}
