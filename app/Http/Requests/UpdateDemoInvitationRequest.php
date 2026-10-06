<?php

namespace App\Http\Requests;

class UpdateDemoInvitationRequest extends StoreInvitationRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }
}
