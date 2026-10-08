<?php

namespace App\Models;

use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory;

    protected $fillable = ['name', 'key', 'view_path', 'thumbnail', 'price', 'category', 'is_active'];

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function demo(): HasOne
    {
        return $this->hasOne(TemplateDemo::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
