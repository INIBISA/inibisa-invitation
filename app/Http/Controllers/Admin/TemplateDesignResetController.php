<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;

class TemplateDesignResetController extends Controller
{
    public function __invoke(Template $template): RedirectResponse
    {
        $template->update(['design_draft_settings' => $template->defaultDesignSettings()]);

        return back()->with('success', 'Desain bawaan disimpan sebagai draft.');
    }
}
