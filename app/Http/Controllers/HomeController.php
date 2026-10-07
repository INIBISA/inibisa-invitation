<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
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
            ->get(['id', 'key', 'name', 'thumbnail']);

        $demos = Invitation::query()
            ->select(['id', 'template_id', 'slug'])
            ->whereIn('template_id', $templates->pluck('id'))
            ->where('status', Invitation::STATUS_PUBLISHED)
            ->orderBy('id')
            ->get()
            ->unique('template_id')
            ->keyBy('template_id');

        $descriptions = [
            'eternal-ivory' => 'Nuansa ivory yang tenang dengan serif anggun dan ornamen floral klasik.',
            'modern-minimalist' => 'Desain bersih dengan ruang lapang, fokus pada tipografi dan informasi acara.',
            'rustic-forest' => 'Nuansa kayu dan dedaunan hangat untuk pernikahan outdoor yang intim.',
            'sweet-blossom' => 'Nuansa blush romantis dengan bunga sakura yang hangat dan manis.',
        ];

        return view('home', compact('templates', 'demos', 'descriptions'));
    }
}
