<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'template_id', 'title', 'slug', 'status', 'data', 'published_at'])]
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_INACTIVE = 'inactive';

    protected $attributes = ['status' => self::STATUS_DRAFT];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(InvitationMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
