<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __invoke(): View
    {
        $templates = Template::query()->withCount('invitations')->with('demo')->orderBy('name')->get();
        $demos = $templates->mapWithKeys(fn (Template $template) => [$template->id => $template->demo]);

        return view('admin.templates', compact('templates', 'demos'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update([...$data, 'is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Template diperbarui.');
    }
}
