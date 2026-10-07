<?php

namespace App\Support;

use Illuminate\Support\Str;

class YouTubeVideo
{
    public static function idFromUrl(?string $url): ?string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        $host = Str::lower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $id = explode('/', $path)[0];
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'www.youtube-nocookie.com'], true)) {
            if (Str::startsWith($path, ['embed/', 'shorts/', 'live/'])) {
                $id = explode('/', $path)[1] ?? null;
            } elseif ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } else {
                return null;
            }
        } else {
            return null;
        }

        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }
}
