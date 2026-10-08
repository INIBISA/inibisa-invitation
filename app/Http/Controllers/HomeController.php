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
            ->get(['id', 'key', 'name', 'thumbnail']);

        $demos = $templates->mapWithKeys(fn (Template $template) => [$template->id => $template->demo]);

        $descriptions = [
            'eternal-ivory' => 'Nuansa ivory yang tenang dengan serif anggun dan ornamen floral klasik.',
            'modern-minimalist' => 'Desain bersih dengan ruang lapang, fokus pada tipografi dan informasi acara.',
            'rustic-forest' => 'Nuansa kayu dan dedaunan hangat untuk pernikahan outdoor yang intim.',
            'sweet-blossom' => 'Nuansa blush romantis dengan bunga sakura yang hangat dan manis.',
            'midnight-nusantara' => 'Nuansa biru malam mewah dengan aksen emas dan geometri Nusantara.',
        ];

        return view('home', compact('templates', 'demos', 'descriptions'));
    }
}
