<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\InvitationMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InvitationMedia> */
class InvitationMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'collection' => 'gallery',
            'file_path' => 'invitations/example/gallery.webp',
            'width' => 960,
            'height' => 1280,
            'sort_order' => 0,
        ];
    }
}
