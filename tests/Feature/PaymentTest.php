<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_pay_for_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $event = Event::factory()->create(['created_by' => $customer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/payment", [
                'amount' => $ticket->price
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => $ticket->price
        ]);
    }

    /** @test */
    public function another_user_cannot_pay_for_someone_elses_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $event = Event::factory()->create(['created_by' => $customer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($other, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/payment", [
                'amount' => $ticket->price
            ]);

        $response->assertStatus(403);
    }


}
