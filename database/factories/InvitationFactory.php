<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use App\Support\InvitationData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Invitation> */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->firstName().' & '.fake()->firstName();

        return [
            'user_id' => User::factory()->active(),
            'template_id' => Template::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(10, 99999),
            'status' => Invitation::STATUS_DRAFT,
            'data' => InvitationData::defaults(),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Invitation::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
