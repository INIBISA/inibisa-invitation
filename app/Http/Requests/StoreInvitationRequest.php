<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\Payment;
use App\Support\YouTubeVideo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->string('slug')->toString())]);
    }

    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        return $invitation instanceof Invitation
            ? ($this->user()?->can('update', $invitation) ?? false)
            : Payment::query()->whereBelongsTo($this->user())->where('template_id', $this->integer('template_id'))->where('status', Payment::STATUS_PAID)->whereNull('invitation_id')->exists();
    }

    public function rules(): array
    {
        $invitation = $this->route('invitation');
        $uniqueSlug = Rule::unique('invitations', 'slug');

        if ($invitation instanceof Invitation) {
            $uniqueSlug->ignore($invitation);
        }

        return [
            'template_id' => ['required', Rule::exists('templates', 'id')->where('is_active', true), Rule::when($invitation instanceof Invitation, [Rule::in([$invitation?->template_id])])],
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:150', Rule::notIn(['admin', 'dashboard', 'login', 'logout', 'register', 'storage', 'up']), $uniqueSlug],
            'groom.nickname' => ['nullable', 'string', 'max:80'],
            'groom.full_name' => ['nullable', 'string', 'max:150'],
            'groom.father' => ['nullable', 'string', 'max:150'],
            'groom.mother' => ['nullable', 'string', 'max:150'],
            'groom.instagram' => ['nullable', 'string', 'max:100'],
            'bride.nickname' => ['nullable', 'string', 'max:80'],
            'bride.full_name' => ['nullable', 'string', 'max:150'],
            'bride.father' => ['nullable', 'string', 'max:150'],
            'bride.mother' => ['nullable', 'string', 'max:150'],
            'bride.instagram' => ['nullable', 'string', 'max:100'],
            'wedding_date' => ['nullable', 'date'],
            'quote' => ['nullable', 'string', 'max:500'],
            'events' => ['nullable', 'array', 'max:5'],
            'events.*.name' => ['required', 'string', 'max:100'],
            'events.*.date' => ['required', 'date'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.location' => ['required', 'string', 'max:150'],
            'events.*.address' => ['required', 'string', 'max:500'],
            'events.*.maps_url' => ['nullable', 'url:http,https', 'max:1000'],
            'stories' => ['nullable', 'array', 'max:10'],
            'stories.*.title' => ['required', 'string', 'max:150'],
            'stories.*.date' => ['nullable', 'date'],
            'stories.*.story' => ['required', 'string', 'max:2000'],
            'banks' => ['nullable', 'array', 'max:5'],
            'banks.*.bank_name' => ['required', 'string', 'max:80'],
            'banks.*.account_number' => ['required', 'string', 'max:80'],
            'banks.*.account_name' => ['required', 'string', 'max:150'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'boolean'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'groom_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'bride_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'qris' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'story_photos' => ['nullable', 'array', 'max:10'],
            'story_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'music' => ['nullable', 'file', 'mimes:mp3,ogg,wav', 'max:10240'],
            'wedding_music_id' => ['nullable', Rule::exists('wedding_music', 'id')->where('is_active', true)],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'music_start_seconds' => ['nullable', 'integer', 'min:0', 'max:43200'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->filled('youtube_url') && ! YouTubeVideo::idFromUrl($this->string('youtube_url')->toString())) {
                $validator->errors()->add('youtube_url', 'Masukkan link video YouTube yang valid.');
            }
        }];
    }
}
