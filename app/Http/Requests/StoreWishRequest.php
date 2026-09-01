<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;

class StoreWishRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        return $invitation instanceof Invitation
            && $invitation->status === Invitation::STATUS_PUBLISHED
            && (bool) data_get($invitation->data, 'settings.wishes', true);
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:600'],
        ];
    }
}
