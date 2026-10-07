<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class YouTubeMusicSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->merge(['q' => trim((string) $request->query('q', ''))]);
        $validated = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);
        $apiKey = config('services.youtube.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            return response()->json(['message' => 'Pencarian YouTube belum tersedia. Gunakan tautan video sementara.'], 503);
        }

        $query = $validated['q'];

        try {
            $results = Cache::remember('youtube-music-search:'.hash('sha256', mb_strtolower($query)), now()->addHours(12), function () use ($apiKey, $query): array {
                $response = Http::acceptJson()->connectTimeout(3)->timeout(8)->get('https://www.googleapis.com/youtube/v3/search', [
                    'part' => 'snippet',
                    'q' => $query,
                    'type' => 'video',
                    'videoEmbeddable' => 'true',
                    'videoSyndicated' => 'true',
                    'regionCode' => 'ID',
                    'maxResults' => 10,
                    'key' => $apiKey,
                ])->throw();

                return collect($response->json('items', []))
                    ->filter(fn (mixed $item): bool => is_array($item) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) data_get($item, 'id.videoId')) === 1)
                    ->map(function (array $item): array {
                        $videoId = $item['id']['videoId'];

                        return [
                            'id' => $videoId,
                            'title' => html_entity_decode((string) data_get($item, 'snippet.title', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'channel' => html_entity_decode((string) data_get($item, 'snippet.channelTitle', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'thumbnail' => 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg',
                            'url' => 'https://www.youtube.com/watch?v='.$videoId,
                        ];
                    })
                    ->values()
                    ->all();
            });
        } catch (ConnectionException|RequestException $exception) {
            return response()->json(['message' => 'Pencarian YouTube sedang tidak tersedia. Coba lagi nanti atau gunakan tautan video.'], 502);
        }

        return response()->json(['results' => $results]);
    }
}
