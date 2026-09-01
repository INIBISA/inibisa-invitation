<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __invoke(): View
    {
        $templates = Template::query()->withCount('invitations')->orderBy('name')->get();

        return view('admin.templates', compact('templates'));
    }
}
