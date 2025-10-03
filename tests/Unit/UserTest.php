<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_many_events()
    {
        $user = User::factory()->create();
        Event::factory()->count(2)->create(['created_by' => $user->id]);
        $this->assertCount(2, $user->events);
    }

    /** @test */
    public function user_has_many_bookings()
    {
        $user = User::factory()->create();
        Booking::factory()->count(3)->create(['user_id' => $user->id]);
        $this->assertCount(3, $user->bookings);
    }

    /** @test */
    public function user_has_many_payments()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);
        Payment::factory()->create(['booking_id' => $booking->id]);

        $this->assertCount(1, $user->payments);
    }
}
