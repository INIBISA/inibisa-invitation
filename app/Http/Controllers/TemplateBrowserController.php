<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateBrowserController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()->where('is_active', true)->with('demo')->orderBy('name')->get();

        return view('templates.index', compact('templates'));
    }

    public function show(Request $request, Template $template): View
    {
        abort_unless($template->is_active && $template->demo, 404);
        $invitation = $template->demo;
        $guestName = $request->string('to', 'Tamu Undangan')->trim()->limit(100)->toString();
        $isPreview = true;

        return view($template->view_path, compact('invitation', 'guestName', 'isPreview'));
    }
}
