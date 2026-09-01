<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Wish> */
class WishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_name' => fake()->name(),
            'message' => fake()->sentence(),
        ];
    }
}
