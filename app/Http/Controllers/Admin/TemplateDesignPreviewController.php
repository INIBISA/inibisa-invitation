<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\View\View;

class TemplateDesignPreviewController extends Controller
{
    public function __invoke(Template $template): View
    {
        abort_unless($template->demo, 404);

        $template->design_settings = $template->draftDesignSettings();
        $invitation = $template->demo;
        $invitation->setRelation('template', $template);
        $guestName = 'Tamu Undangan';
        $isPreview = true;

        return view($template->view_path, compact('invitation', 'guestName', 'isPreview'));
    }
}
