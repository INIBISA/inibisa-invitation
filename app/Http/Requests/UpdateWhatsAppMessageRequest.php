<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\WhatsAppInvitationMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateWhatsAppMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_CUSTOMER;
    }

    public function rules(): array
    {
        return ['message_template' => ['required', 'string', 'max:5000']];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            preg_match_all('/\{[^{}]+\}/u', $this->string('message_template')->toString(), $matches);
            $unknown = array_diff(array_unique($matches[0]), WhatsAppInvitationMessage::PLACEHOLDERS);

            if ($unknown !== []) {
                $validator->errors()->add('message_template', 'Placeholder tidak dikenal: '.implode(', ', $unknown).'.');
            }
        }];
    }
}
