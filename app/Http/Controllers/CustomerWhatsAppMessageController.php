<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWhatsAppMessageRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerWhatsAppMessageController extends Controller
{
    public function update(UpdateWhatsAppMessageRequest $request): RedirectResponse
    {
        $request->user()->update(['whatsapp_message_template' => $request->string('message_template')->toString()]);

        return back()->with('success', 'Template pesan WhatsApp disimpan untuk semua undangan Anda.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === User::ROLE_CUSTOMER, 403);
        $request->user()->update(['whatsapp_message_template' => null]);

        return back()->with('success', 'Template pesan WhatsApp dikembalikan ke default.');
    }
}
