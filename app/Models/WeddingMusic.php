<?php

namespace App\Models;

use Database\Factories\WeddingMusicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeddingMusic extends Model
{
    /** @use HasFactory<WeddingMusicFactory> */
    use HasFactory;

    protected $table = 'wedding_music';

    protected $fillable = ['title', 'category', 'youtube_url', 'youtube_video_id', 'thumbnail', 'is_active'];

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
