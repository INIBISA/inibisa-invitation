<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('guest') instanceof Guest && ($this->user()?->can('update', $this->route('guest')->invitation) ?? false);
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:150'], 'whatsapp' => ['nullable', 'regex:/^\+?[0-9]{8,15}$/']];
    }
}
