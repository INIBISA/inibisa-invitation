<?php

namespace App\Models;

use Database\Factories\WishFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invitation_id', 'guest_name', 'message'])]
class Wish extends Model
{
    /** @use HasFactory<WishFactory> */
    use HasFactory;

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
