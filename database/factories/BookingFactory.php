<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Ticket;

class BookingFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),       // automatski kreira user-a
            'ticket_id' => Ticket::factory(),   // automatski kreira ticket
            'quantity' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
