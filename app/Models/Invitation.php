<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Invitation extends Pivot
{
    /** @use HasFactory<\Database\Factories\InvitationFactory> */
    use HasFactory;
}
