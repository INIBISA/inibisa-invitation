<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rsvp> */
class RsvpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_name' => fake()->name(),
            'attendance' => fake()->randomElement(['attending', 'not_attending']),
            'guest_count' => fake()->numberBetween(1, 5),
        ];
    }
}
