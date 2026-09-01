<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        return $invitation instanceof Invitation
            && $invitation->status === Invitation::STATUS_PUBLISHED
            && (bool) data_get($invitation->data, 'settings.rsvp', true);
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:100'],
            'attendance' => ['required', Rule::in(['attending', 'not_attending'])],
            'guest_count' => ['required_if:attendance,attending', 'integer', 'min:1', 'max:10'],
        ];
    }
}
