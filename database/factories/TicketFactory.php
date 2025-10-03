<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

class TicketFactory extends Factory
{
    public function definition()
    {
        return [
            'event_id' => Event::factory(), // automatski kreira event
            'type' => $this->faker->randomElement(['Regular', 'VIP', 'EarlyBird']),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'quantity' => $this->faker->numberBetween(50, 200),
        ];
    }
}
