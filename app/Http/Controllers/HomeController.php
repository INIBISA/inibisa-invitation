<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        $templates = Template::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->with('demo:id,template_id,slug')
            ->get(['id', 'key', 'name', 'thumbnail', 'price', 'description']);

        $demos = $templates->mapWithKeys(fn (Template $template) => [$template->id => $template->demo]);
        $featuredDemo = $templates->first(fn (Template $template): bool => $template->key === 'eternal-ivory' && $template->demo !== null)
            ?? $templates->first(fn (Template $template): bool => $template->demo !== null);

        return view('home', compact('templates', 'demos', 'featuredDemo'));
    }
}
