<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\Payment;
use App\Support\YouTubeVideo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveInvitationStepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        if ($invitation instanceof Invitation) {
            return $this->user()?->can('update', $invitation) ?? false;
        }

        return $this->integer('step') === 1
            && Payment::query()->whereBelongsTo($this->user())->where('template_id', $this->integer('template_id'))->where('status', Payment::STATUS_PAID)->whereNull('invitation_id')->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $step = $this->integer('step');
        $invitation = $this->route('invitation');
        $uniqueSlug = Rule::unique('invitations', 'slug');

        if ($invitation instanceof Invitation) {
            $uniqueSlug->ignore($invitation);
        }

        return match ($step) {
            1 => [
                'step' => ['required', Rule::in([1])],
                'template_id' => ['required', Rule::exists('templates', 'id')->where('is_active', true), Rule::when($invitation instanceof Invitation, [Rule::in([$invitation?->template_id])])],
                'title' => ['required', 'string', 'max:150'],
                'slug' => ['required', 'alpha_dash:ascii', 'max:150', Rule::notIn(['admin', 'dashboard', 'login', 'logout', 'register', 'storage', 'up']), $uniqueSlug],
            ],
            2 => array_merge(['step' => ['required', Rule::in([2])]], $this->coupleRules()),
            3 => [
                'step' => ['required', Rule::in([3])],
                'wedding_date' => ['nullable', 'date'],
                'quote' => ['nullable', 'string', 'max:500'],
                'events' => ['nullable', 'array', 'max:5'],
                'events.*.name' => ['required', 'string', 'max:100'],
                'events.*.date' => ['required', 'date'],
                'events.*.time' => ['required', 'date_format:H:i'],
                'events.*.location' => ['required', 'string', 'max:150'],
                'events.*.address' => ['required', 'string', 'max:500'],
                'events.*.maps_url' => ['nullable', 'url:http,https', 'max:1000'],
            ],
            4 => [
                'step' => ['required', Rule::in([4])],
                'stories' => ['nullable', 'array', 'max:10'],
                'stories.*.title' => ['required', 'string', 'max:150'],
                'stories.*.date' => ['nullable', 'date'],
                'stories.*.story' => ['required', 'string', 'max:2000'],
                'banks' => ['nullable', 'array', 'max:5'],
                'banks.*.bank_name' => ['required', 'string', 'max:80'],
                'banks.*.account_number' => ['required', 'string', 'max:80'],
                'banks.*.account_name' => ['required', 'string', 'max:150'],
                'story_photos' => ['nullable', 'array', 'max:10'],
                'story_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ],
            5 => [
                'step' => ['required', Rule::in([5])],
                'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'qris' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
                'gallery' => ['nullable', 'array', 'max:12'],
                'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'wedding_music_id' => ['nullable', Rule::exists('wedding_music', 'id')->where('is_active', true)],
                'youtube_url' => ['nullable', 'string', 'max:255'],
                'music_start_seconds' => ['nullable', 'integer', 'min:0', 'max:43200'],
            ],
            6 => [
                'step' => ['required', Rule::in([6])],
                'settings' => ['nullable', 'array'],
                'settings.*' => ['nullable', 'boolean'],
            ],
            default => ['step' => ['required', Rule::in(range(1, 6))]],
        };
    }

    protected function prepareForValidation(): void
    {
        if ($this->integer('step') === 1) {
            $this->merge(['slug' => Str::slug($this->string('slug')->toString())]);
        }
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->integer('step') === 5 && $this->filled('youtube_url') && ! YouTubeVideo::idFromUrl($this->string('youtube_url')->toString())) {
                $validator->errors()->add('youtube_url', 'Masukkan link video YouTube yang valid.');
            }
        }];
    }

    /** @return array<string, array<int, ValidationRule|string>> */
    private function coupleRules(): array
    {
        $rules = ['step' => ['required', Rule::in([2])]];

        foreach (['groom', 'bride'] as $person) {
            $rules[$person.'.nickname'] = ['nullable', 'string', 'max:80'];
            $rules[$person.'.full_name'] = ['nullable', 'string', 'max:150'];
            $rules[$person.'.father'] = ['nullable', 'string', 'max:150'];
            $rules[$person.'.mother'] = ['nullable', 'string', 'max:150'];
            $rules[$person.'.instagram'] = ['nullable', 'string', 'max:100'];
            $rules[$person.'_photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        }

        return $rules;
    }
}
