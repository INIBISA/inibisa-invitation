<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ImportGuestsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('invitation') instanceof Invitation
            && ($this->user()?->can('update', $this->route('invitation')) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_file' => ['required', File::types(['xlsx', 'xls', 'csv'])->max('2mb')],
        ];
    }
}
