<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeddingMusic;
use App\Support\YouTubeVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WeddingMusicController extends Controller
{
    public function index(Request $request): View
    {
        $music = WeddingMusic::query()
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search')->toString().'%'))
            ->orderBy('category')->orderBy('title')->paginate(20)->withQueryString();

        return view('admin.music.index', compact('music'));
    }

    public function store(Request $request): RedirectResponse
    {
        WeddingMusic::query()->create($this->validatedData($request));

        return back()->with('success', 'Musik ditambahkan.');
    }

    public function update(Request $request, WeddingMusic $music): RedirectResponse
    {
        $music->update($this->validatedData($request));

        return back()->with('success', 'Musik diperbarui.');
    }

    public function destroy(WeddingMusic $music): RedirectResponse
    {
        $music->delete();

        return back()->with('success', 'Musik dihapus.');
    }

    /** @return array{title: string, category: string, youtube_url: string, youtube_video_id: string, thumbnail: string, is_active: bool} */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'youtube_url' => ['required', 'url:http,https', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $videoId = YouTubeVideo::idFromUrl($data['youtube_url']);

        if (! $videoId) {
            throw ValidationException::withMessages(['youtube_url' => 'Masukkan link video YouTube yang valid.']);
        }

        return [
            ...$data,
            'youtube_video_id' => $videoId,
            'thumbnail' => 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg',
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
