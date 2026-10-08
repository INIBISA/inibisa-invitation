<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveInvitationStepRequest;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\WeddingMusic;
use App\Services\InvitationMediaManager;
use App\Support\InvitationData;
use App\Support\YouTubeVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InvitationStepController extends Controller
{
    public function store(SaveInvitationStepRequest $request): JsonResponse
    {
        $invitation = DB::transaction(function () use ($request): Invitation {
            $payment = Payment::query()
                ->whereBelongsTo($request->user())
                ->where('template_id', $request->integer('template_id'))
                ->where('status', Payment::STATUS_PAID)
                ->whereNull('invitation_id')
                ->lockForUpdate()
                ->firstOrFail();
            $invitation = $request->user()->invitations()->create([
                'template_id' => $request->integer('template_id'),
                'title' => $request->string('title')->toString(),
                'slug' => $request->string('slug')->toString(),
                'editing_step' => 2,
                'data' => InvitationData::defaults(),
            ]);
            $payment->update(['invitation_id' => $invitation->id]);

            return $invitation;
        }, attempts: 3);

        return $this->response($invitation, 1);
    }

    public function update(SaveInvitationStepRequest $request, Invitation $invitation, int $step, InvitationMediaManager $mediaManager): JsonResponse
    {
        abort_unless($step === $request->integer('step') && $step >= 1 && $step <= 6, 404);
        $validated = $request->validated();
        $data = $invitation->data ?? InvitationData::defaults();

        if ($step === 1) {
            $invitation->fill([
                'title' => $request->string('title')->toString(),
                'slug' => $request->string('slug')->toString(),
            ]);
        } elseif ($step === 2) {
            $data = array_replace_recursive($data, Arr::only($validated, ['groom', 'bride']));
        } elseif ($step === 3) {
            foreach (['wedding_date', 'quote', 'events'] as $key) {
                $data[$key] = $validated[$key] ?? ($key === 'events' ? [] : '');
            }
        } elseif ($step === 4) {
            $data['stories'] = $validated['stories'] ?? [];
            $data['banks'] = $validated['banks'] ?? [];
        } elseif ($step === 5) {
            $this->mergeMusic($invitation, $data, $validated);
        } else {
            foreach (array_keys(InvitationData::defaults()['settings']) as $setting) {
                $data['settings'][$setting] = (bool) Arr::get($validated, 'settings.'.$setting, false);
            }
        }

        $invitation->data = $data;
        $invitation->editing_step = max($invitation->editing_step, min(6, $step + 1));
        $invitation->save();

        if (in_array($step, [2, 4, 5], true)) {
            $mediaManager->syncFromRequest($invitation, $request);
        }

        return $this->response($invitation, $step);
    }

    /** @param array<string, mixed> $data @param array<string, mixed> $validated */
    private function mergeMusic(Invitation $invitation, array &$data, array $validated): void
    {
        $music = isset($validated['wedding_music_id'])
            ? WeddingMusic::query()->where('is_active', true)->find($validated['wedding_music_id'])
            : null;
        $url = trim($validated['youtube_url'] ?? '');
        $startSeconds = (int) ($validated['music_start_seconds'] ?? 0);

        if ($url !== '') {
            $invitation->wedding_music_id = null;
            $data['music'] = ['youtube_url' => $url, 'youtube_video_id' => YouTubeVideo::idFromUrl($url), 'title' => 'Musik pilihan Anda', 'start_seconds' => $startSeconds];
        } elseif ($music) {
            $invitation->wedding_music_id = $music->id;
            $data['music'] = ['youtube_url' => $music->youtube_url, 'youtube_video_id' => $music->youtube_video_id, 'title' => $music->title, 'start_seconds' => $startSeconds];
        } else {
            $invitation->wedding_music_id = null;
            $data['music'] = ['youtube_url' => null, 'youtube_video_id' => null, 'title' => null, 'start_seconds' => $startSeconds];
        }
    }

    private function response(Invitation $invitation, int $savedStep): JsonResponse
    {
        return response()->json([
            'message' => $savedStep === 6 ? 'Undangan selesai disimpan.' : 'Langkah '.$savedStep.' berhasil disimpan.',
            'invitation_id' => $invitation->id,
            'edit_url' => route('invitations.edit', $invitation),
            'step_url' => route('invitations.steps.update', ['invitation' => $invitation, 'step' => '__STEP__']),
            'next_step' => min(6, $savedStep + 1),
        ]);
    }
}
