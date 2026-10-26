<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Support\InvitationData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VisualTemplateController extends Controller
{
    public function create(): View
    {
        return view('admin.templates-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
        ]);

        $template = DB::transaction(function () use ($validated): Template {
            $template = Template::query()->create([
                ...$validated,
                'key' => 'visual-'.Str::lower(Str::random(12)),
                'view_path' => 'templates.visual.index',
                'thumbnail' => 'images/templates/visual-placeholder.svg',
                'is_active' => false,
            ]);

            $data = InvitationData::defaults();
            $data['groom']['nickname'] = 'Arga';
            $data['groom']['full_name'] = 'Arga Pratama';
            $data['bride']['nickname'] = 'Nadia';
            $data['bride']['full_name'] = 'Nadia Putri';
            $data['wedding_date'] = now()->addMonths(3)->toDateString();
            $data['quote'] = 'Bersama, kami memulai cerita baru.';
            $data['events'] = [['name' => 'Resepsi', 'date' => $data['wedding_date'], 'time' => '11:00', 'location' => 'Gedung Serbaguna', 'address' => 'Jakarta', 'maps_url' => '']];

            $template->demo()->create([
                'slug' => 'visual-demo-'.$template->id,
                'title' => 'Arga & Nadia',
                'data' => $data,
            ]);

            return $template;
        });

        return redirect()->route('admin.templates.canvas.edit', $template)->with('success', 'Templat visual dibuat. Mulai susun desainnya.');
    }
}
