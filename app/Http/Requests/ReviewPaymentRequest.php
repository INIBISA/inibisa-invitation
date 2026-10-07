<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Payment::STATUS_PAID, Payment::STATUS_REJECTED])],
            'rejection_reason' => ['required_if:status,'.Payment::STATUS_REJECTED, 'nullable', 'string', 'max:1000'],
        ];
    }
}
