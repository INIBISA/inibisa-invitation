<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadInvitationImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        if ($invitation instanceof Invitation) {
            return $this->user()?->can('update', $invitation) ?? false;
        }

        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'collection' => ['required', Rule::in(['cover', 'groom', 'bride', 'qris', 'gallery', 'story'])],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.($this->input('collection') === 'qris' ? 3072 : 5120)],
            'replace' => ['required', 'boolean'],
        ];
    }
}
