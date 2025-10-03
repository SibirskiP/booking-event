<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function booking_belongs_to_user()
    {
        $user = User::factory()->create();
        $organizer = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->for($organizer, 'creator')->create(); // OVDJE dodajemo creator
        $ticket = Ticket::factory()->for($event, 'event')->create();
        $booking = Booking::factory()->for($user, 'user')->for($ticket, 'ticket')->create();

        $this->assertInstanceOf(User::class, $booking->user);
    }

    /** @test */
    public function booking_belongs_to_ticket()
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->for($organizer, 'creator')->create();
        $ticket = Ticket::factory()->for($event, 'event')->create();
        $booking = Booking::factory()->for(User::factory(), 'user')->for($ticket, 'ticket')->create();

        $this->assertInstanceOf(Ticket::class, $booking->ticket);
    }


    /** @test */
    public function booking_has_one_payment()
    {
        $user = User::factory()->create();
        $organizer = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->for($organizer, 'creator')->create();
        $ticket = Ticket::factory()->for($event, 'event')->create();
        $booking = Booking::factory()->for($user, 'user')->for($ticket, 'ticket')->create();

        // kreiraj payment preko factory-ja
        $payment = Payment::factory()->for($booking, 'booking')->create();

        $this->assertInstanceOf(Payment::class, $booking->payment);
    }
}
