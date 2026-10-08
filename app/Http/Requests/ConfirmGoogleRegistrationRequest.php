<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmGoogleRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has(GoogleAuthController::SESSION_KEY);
    }

    public function rules(): array
    {
        return [
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'terms.accepted' => 'Anda harus menyetujui Syarat dan Ketentuan serta Kebijakan Privasi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'terms' => 'persetujuan Syarat dan Ketentuan',
        ];
    }
}
