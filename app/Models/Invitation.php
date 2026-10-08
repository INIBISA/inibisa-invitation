<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'wedding_music_id',
        'title',
        'slug',
        'status',
        'editing_step',
        'data',
        'published_at',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_INACTIVE = 'inactive';

    protected $attributes = ['status' => self::STATUS_DRAFT];

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draf',
            self::STATUS_PUBLISHED => 'Dipublikasikan',
            self::STATUS_INACTIVE => 'Nonaktif',
            default => ucfirst($this->status),
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function weddingMusic(): BelongsTo
    {
        return $this->belongsTo(WeddingMusic::class);
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

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'editing_step' => 'integer',
            'published_at' => 'datetime',
        ];
    }
}
