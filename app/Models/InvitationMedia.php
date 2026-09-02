<?php

namespace App\Models;

use Database\Factories\InvitationMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationMedia extends Model
{
    /** @use HasFactory<InvitationMediaFactory> */
    use HasFactory;

    protected $fillable = ['invitation_id', 'collection', 'file_path', 'width', 'height', 'sort_order'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
