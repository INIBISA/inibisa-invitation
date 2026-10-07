<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', Rule::exists('templates', 'id')->where('is_active', true)],
            'payment_method' => ['prohibited'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $method = Setting::activePaymentMethod();

            if ($method === Payment::METHOD_MANUAL_TRANSFER
                && ! Setting::query()->where('key', 'manual_transfer')->whereNotNull('value')->where('value', '!=', '')->exists()) {
                $validator->errors()->add('template_id', 'Transfer manual belum dikonfigurasi admin.');
            }

            if ($method === Payment::METHOD_MIDTRANS
                && (! config('payments.midtrans.server_key') || ! config('payments.midtrans.client_key'))) {
                $validator->errors()->add('template_id', 'Midtrans belum dikonfigurasi admin.');
            }
        }];
    }
}
