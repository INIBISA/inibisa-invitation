<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;

class StoreGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('invitation') instanceof Invitation && ($this->user()?->can('update', $this->route('invitation')) ?? false);
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:150'], 'whatsapp' => ['nullable', 'regex:/^\+?[0-9]{8,15}$/']];
    }
}
