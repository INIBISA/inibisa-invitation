<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TemplateDemo extends Model
{
    protected $fillable = ['template_id', 'slug', 'title', 'data'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /** @return Collection<int, mixed> */
    public function getMediaAttribute(): Collection
    {
        return collect($this->data['media'] ?? [])->map(fn (array $media): object => (object) $media);
    }

    /** @return Collection<int, mixed> */
    public function getWishesAttribute(): Collection
    {
        return collect($this->data['wishes'] ?? [])->map(fn (array $wish): object => (object) [
            ...$wish,
            'created_at' => Carbon::parse($wish['created_at'] ?? $this->updated_at),
        ]);
    }

    protected function casts(): array
    {
        return ['data' => 'array'];
    }
}
