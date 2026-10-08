<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YouTubeMusicSearchTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_search_youtube_music(): void
    {
        $this->getJson(route('youtube.music.search', ['q' => 'wedding music']))
            ->assertUnauthorized();
    }

    public function test_admin_catalog_offers_youtube_search(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.music.index'))
            ->assertOk()
            ->assertSee('Cari musik di YouTube');
    }

    public function test_search_requires_a_configured_api_key(): void
    {
        $customer = User::factory()->active()->create();
        config()->set('services.youtube.api_key', null);
        Http::preventStrayRequests();

        $this->actingAs($customer)->getJson(route('youtube.music.search', ['q' => 'wedding music']))
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'Pencarian YouTube belum tersedia. Gunakan tautan video sementara.');

        Http::assertNothingSent();
    }

    public function test_customer_can_search_and_choose_embeddable_youtube_video(): void
    {
        $customer = User::factory()->active()->create();
        config()->set('services.youtube.api_key', 'test-api-key');
        Http::preventStrayRequests();
        Http::fake([
            'www.googleapis.com/youtube/v3/search*' => Http::response([
                'items' => [
                    ['id' => ['videoId' => 'laMRBmD2aeg'], 'snippet' => ['title' => 'Lagu &amp; Cinta', 'channelTitle' => 'Kanal Musik']],
                    ['id' => ['videoId' => 'invalid'], 'snippet' => ['title' => 'Tidak valid', 'channelTitle' => 'Kanal Lain']],
                ],
            ]),
        ]);

        $response = $this->actingAs($customer)->getJson(route('youtube.music.search', ['q' => '  Lagu Cinta  ']));

        $response->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', 'laMRBmD2aeg')
            ->assertJsonPath('results.0.title', 'Lagu & Cinta')
            ->assertJsonPath('results.0.url', 'https://www.youtube.com/watch?v=laMRBmD2aeg');

        Http::assertSent(function (Request $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $query['q'] === 'Lagu Cinta'
                && $query['type'] === 'video'
                && $query['videoEmbeddable'] === 'true'
                && $query['videoSyndicated'] === 'true'
                && $query['key'] === 'test-api-key';
        });

        $this->actingAs($customer)->getJson(route('youtube.music.search', ['q' => 'Lagu Cinta']))
            ->assertJsonPath('results.0.id', 'laMRBmD2aeg');
        Http::assertSentCount(1);
    }

    public function test_invalid_search_query_is_rejected_before_calling_youtube(): void
    {
        $customer = User::factory()->active()->create();
        config()->set('services.youtube.api_key', 'test-api-key');
        Http::preventStrayRequests();

        $this->actingAs($customer)->getJson(route('youtube.music.search', ['q' => 'a']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('q');

        Http::assertNothingSent();
    }

    public function test_youtube_failure_shows_recoverable_error(): void
    {
        $customer = User::factory()->active()->create();
        config()->set('services.youtube.api_key', 'test-api-key');
        Http::preventStrayRequests();
        Http::fake(['www.googleapis.com/youtube/v3/search*' => Http::response(['error' => ['message' => 'quota exceeded']], 403)]);

        $this->actingAs($customer)->getJson(route('youtube.music.search', ['q' => 'music unavailable']))
            ->assertStatus(502)
            ->assertJsonPath('message', 'Pencarian YouTube sedang tidak tersedia. Coba lagi nanti atau gunakan tautan video.');

        Http::assertSentCount(1);
    }
}
