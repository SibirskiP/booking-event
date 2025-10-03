<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingConfirmed;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_book_ticket()
    {
        Notification::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 2
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'ticket_id' => $ticket->id,
            'user_id' => $customer->id
        ]);
    }


    /** @test */
    public function customer_cannot_book_more_than_available_tickets()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'quantity' => 5]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 10
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function customer_can_delete_their_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    /** @test */
    public function customer_cannot_delete_others_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $booking = Booking::factory()->create([
            'user_id' => $otherCustomer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(403); // forbidden
    }

    /** @test */
    public function quantity_is_required_to_book_ticket()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/tickets/{$ticket->id}/bookings", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('quantity');
    }

    /** @test */
    public function customer_can_view_their_bookings()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson("/api/bookings");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $booking->id]);
    }



    /** @test */




    /** @test */
    public function customer_cannot_pay_others_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);
        $booking = Booking::factory()->create([
            'user_id' => $otherCustomer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson("/api/bookings/{$booking->id}/payment");

        $response->assertStatus(403);
    }


    /** @test */
    public function deleting_non_existing_booking_returns_404()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson("/api/bookings/9999"); // ne postoji

        $response->assertStatus(404);
    }


}
