<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;

class PaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => (string) $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
