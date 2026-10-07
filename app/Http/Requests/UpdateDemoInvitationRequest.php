<?php

namespace App\Http\Requests;

use App\Models\TemplateDemo;
use App\Support\YouTubeVideo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateDemoInvitationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->string('slug')->toString())]);
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var TemplateDemo $demo */
        $demo = $this->route('demo');

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:150', Rule::unique('template_demos', 'slug')->ignore($demo)],
            'groom.nickname' => ['required', 'string', 'max:80'],
            'groom.full_name' => ['required', 'string', 'max:150'],
            'groom.father' => ['nullable', 'string', 'max:150'],
            'groom.mother' => ['nullable', 'string', 'max:150'],
            'groom.instagram' => ['nullable', 'string', 'max:100'],
            'bride.nickname' => ['required', 'string', 'max:80'],
            'bride.full_name' => ['required', 'string', 'max:150'],
            'bride.father' => ['nullable', 'string', 'max:150'],
            'bride.mother' => ['nullable', 'string', 'max:150'],
            'bride.instagram' => ['nullable', 'string', 'max:100'],
            'wedding_date' => ['required', 'date'],
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
            'wishes' => ['nullable', 'array', 'max:10'],
            'wishes.*.guest_name' => ['required', 'string', 'max:100'],
            'wishes.*.message' => ['required', 'string', 'max:1000'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'boolean'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'music_start_seconds' => ['nullable', 'integer', 'min:0', 'max:43200'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'groom_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'bride_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'qris' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'story_photos' => ['nullable', 'array', 'max:10'],
            'story_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => [Rule::in(['cover', 'groom', 'bride', 'qris', 'gallery', 'story'])],
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
