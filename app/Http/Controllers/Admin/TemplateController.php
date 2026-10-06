<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Template;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __invoke(): View
    {
        $templates = Template::query()->withCount('invitations')->orderBy('name')->get();

        $demos = Invitation::query()
            ->select(['id', 'template_id', 'slug'])
            ->where('status', Invitation::STATUS_PUBLISHED)
            ->orderBy('id')
            ->get()
            ->unique('template_id')
            ->keyBy('template_id');

        return view('admin.templates', compact('templates', 'demos'));
    }
}
