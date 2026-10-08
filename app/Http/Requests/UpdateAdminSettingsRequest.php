<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminSettingsRequest extends FormRequest
{
    private bool $updatesPaymentSettings = false;

    protected function prepareForValidation(): void
    {
        $this->updatesPaymentSettings = $this->has('active_payment_method') || $this->has('manual_transfer');
        $this->merge([
            'active_payment_method' => $this->input('active_payment_method', Setting::activePaymentMethod()),
            'manual_transfer' => $this->input('manual_transfer', Setting::query()->where('key', 'manual_transfer')->value('value')),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user())],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'active_payment_method' => ['required', Rule::in([Payment::METHOD_MANUAL_TRANSFER, Payment::METHOD_MIDTRANS])],
            'manual_transfer' => ['nullable', Rule::requiredIf($this->updatesPaymentSettings && $this->input('active_payment_method') === Payment::METHOD_MANUAL_TRANSFER), 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->input('active_payment_method') === Payment::METHOD_MIDTRANS
                && (! config('payments.midtrans.server_key') || ! config('payments.midtrans.client_key'))) {
                $validator->errors()->add('active_payment_method', 'Midtrans belum dapat diaktifkan karena server key dan client key belum dikonfigurasi.');
            }
        }];
    }
}
