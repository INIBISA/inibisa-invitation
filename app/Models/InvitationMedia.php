<?php

namespace App\Models;

use Database\Factories\InvitationMediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invitation_id', 'collection', 'file_path', 'width', 'height', 'sort_order'])]
class InvitationMedia extends Model
{
    /** @use HasFactory<InvitationMediaFactory> */
    use HasFactory;

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
