<?php

namespace App\Models;

use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory;

    protected $fillable = ['invitation_id', 'name', 'whatsapp', 'sent_at'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }
}
