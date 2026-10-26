<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;

class TemplateDesignDraftController extends Controller
{
    public function destroy(Template $template): RedirectResponse
    {
        abort_if($template->design_draft_settings === null, 422, 'Draft desain belum tersedia.');

        $template->update(['design_draft_settings' => null]);

        return back()->with('success', 'Draft desain dibuang.');
    }
}
