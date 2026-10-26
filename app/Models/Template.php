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

    protected $fillable = ['name', 'key', 'view_path', 'thumbnail', 'price', 'category', 'design_settings', 'design_draft_settings', 'is_active'];

    /**
     * @return array{primary_color: string, text_color: string, background_color: string, heading_font: string, body_font: string, card_radius: int, hero_overlay: int, sections: array<string, bool>, section_order: list<string>, canvas?: array<string, mixed>}
     */
    public function designSettings(): array
    {
        return $this->resolveDesignSettings($this->design_settings);
    }

    /**
     * @return array{primary_color: string, text_color: string, background_color: string, heading_font: string, body_font: string, card_radius: int, hero_overlay: int, sections: array<string, bool>, section_order: list<string>, canvas?: array<string, mixed>}
     */
    public function draftDesignSettings(): array
    {
        return $this->resolveDesignSettings($this->design_draft_settings ?? $this->design_settings);
    }

    /**
     * @return array{primary_color: string, text_color: string, background_color: string, heading_font: string, body_font: string, card_radius: int, hero_overlay: int, sections: array<string, bool>, section_order: list<string>, canvas?: array<string, mixed>}
     */
    public function defaultDesignSettings(): array
    {
        return $this->resolveDesignSettings(null);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array{primary_color: string, text_color: string, background_color: string, heading_font: string, body_font: string, card_radius: int, hero_overlay: int, sections: array<string, bool>, section_order: list<string>, canvas?: array<string, mixed>}
     */
    public function normalizedDesignSettings(array $settings): array
    {
        return $this->resolveDesignSettings($settings);
    }

    public function designRevisions(): HasMany
    {
        return $this->hasMany(TemplateDesignRevision::class)->latest('id');
    }

    /** @return array<string, string> */
    public function orderableSections(): array
    {
        return match ($this->key) {
            'midnight-nusantara' => ['couple' => 'Pasangan', 'agenda' => 'Agenda', 'story' => 'Cerita', 'gallery' => 'Galeri', 'guest' => 'RSVP & Hadiah', 'wishes' => 'Ucapan'],
            'voxel-voyage' => ['partners' => 'Pasangan', 'missions' => 'Acara', 'map' => 'Cerita', 'inventory' => 'Galeri', 'station' => 'RSVP & Hadiah', 'messages' => 'Ucapan'],
            default => ['quote' => 'Quote', 'couple' => 'Pasangan', 'countdown' => 'Countdown', 'events' => 'Acara', 'story' => 'Cerita', 'gallery' => 'Galeri', 'rsvp' => 'RSVP', 'gift' => 'Hadiah', 'wishes' => 'Ucapan'],
        };
    }

    public function isVisual(): bool
    {
        return $this->view_path === 'templates.visual.index';
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array{primary_color: string, text_color: string, background_color: string, heading_font: string, body_font: string, card_radius: int, hero_overlay: int, sections: array<string, bool>, section_order: list<string>, canvas?: array<string, mixed>}
     */
    private function resolveDesignSettings(?array $settings): array
    {
        $defaults = match ($this->key) {
            'sweet-blossom' => ['primary_color' => '#c87972', 'text_color' => '#5e3f3a', 'background_color' => '#fff7f3', 'heading_font' => 'serif', 'body_font' => 'sans', 'card_radius' => 12, 'hero_overlay' => 52],
            'rustic-forest' => ['primary_color' => '#9d7b45', 'text_color' => '#2f2a21', 'background_color' => '#efe7d8', 'heading_font' => 'serif', 'body_font' => 'sans', 'card_radius' => 8, 'hero_overlay' => 55],
            'modern-minimalist' => ['primary_color' => '#4f6f78', 'text_color' => '#1f2326', 'background_color' => '#f7f5f1', 'heading_font' => 'serif', 'body_font' => 'sans', 'card_radius' => 10, 'hero_overlay' => 35],
            'midnight-nusantara' => ['primary_color' => '#d6b66f', 'text_color' => '#f5ead3', 'background_color' => '#07111f', 'heading_font' => 'serif', 'body_font' => 'sans', 'card_radius' => 0, 'hero_overlay' => 55],
            'voxel-voyage' => ['primary_color' => '#ffd447', 'text_color' => '#172033', 'background_color' => '#fff8df', 'heading_font' => 'monospace', 'body_font' => 'sans', 'card_radius' => 0, 'hero_overlay' => 20],
            default => ['primary_color' => '#b8965b', 'text_color' => '#24211e', 'background_color' => '#f8f4ed', 'heading_font' => 'serif', 'body_font' => 'sans', 'card_radius' => 0, 'hero_overlay' => 52],
        };

        $defaults['sections'] = array_fill_keys(['quote', 'countdown', 'events', 'story', 'gallery', 'rsvp', 'gift', 'wishes'], true);
        if ($this->isVisual()) {
            $defaults['canvas'] = array_fill_keys(
                ['cover', ...array_keys($this->orderableSections()), 'closing'],
                ['background' => $defaults['background_color'], 'height' => 620, 'content_x' => 10, 'content_top' => 0, 'content_width' => 80, 'content_align' => 'center', 'elements' => []],
            );
        }
        $defaultOrder = array_keys($this->orderableSections());
        $requestedOrder = array_values(array_unique(array_filter(
            $settings['section_order'] ?? [],
            fn (mixed $section): bool => is_string($section) && in_array($section, $defaultOrder, true),
        )));
        $settings['section_order'] = [...$requestedOrder, ...array_diff($defaultOrder, $requestedOrder)];

        return array_replace_recursive($defaults, $settings ?? []);
    }

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
        return ['design_settings' => 'array', 'design_draft_settings' => 'array', 'is_active' => 'boolean'];
    }
}
