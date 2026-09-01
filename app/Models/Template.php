<?php

namespace App\Models;

use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'key', 'view_path', 'thumbnail', 'is_active'])]
class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory;

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
