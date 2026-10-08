<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'template_id' => Template::factory(),
            'payment_method' => Payment::METHOD_MANUAL_TRANSFER,
            'amount' => 150000,
            'status' => Payment::STATUS_PENDING,
            'transaction_id' => fake()->unique()->uuid(),
        ];
    }
}
