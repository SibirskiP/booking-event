<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected $organizer;
    protected $customer;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organizer = User::factory()->create(['role' => 'organizer']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->event = Event::factory()->create(['created_by' => $this->organizer->id]);
    }

    /** @test */
    public function organizer_can_create_ticket()
    {
        $response = $this->actingAs($this->organizer, 'sanctum')
            ->postJson("/api/events/{$this->event->id}/tickets", [
                'type' => 'VIP',
                'price' => 100,
                'quantity' => 10
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['type' => 'VIP', 'event_id' => $this->event->id]);
    }

    /** @test */
    public function customer_cannot_create_ticket()
    {
        $response = $this->actingAs($this->customer, 'sanctum')
            ->postJson("/api/events/{$this->event->id}/tickets", [
                'type' => 'VIP',
                'price' => 100,
                'quantity' => 10
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function anyone_can_view_tickets()
    {
        Ticket::factory()->count(5)->create(['event_id' => $this->event->id]);

        $response = $this->getJson("/api/events/{$this->event->id}/tickets");
        $response->assertStatus(200)->assertJsonCount(5, 'data');
    }



    /** @test */
    public function organizer_can_update_ticket()
    {
        $ticket = Ticket::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->organizer, 'sanctum')
            ->putJson("/api/events/{$this->event->id}/tickets/{$ticket->id}", [
                'price' => 150
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'price' => 150
        ]);
    }

    /** @test */
    public function customer_cannot_update_ticket()
    {
        $ticket = Ticket::factory()->create(['event_id' => $this->event->id]);

        $response = $this->actingAs($this->customer, 'sanctum')
            ->putJson("/api/events/{$this->event->id}/tickets/{$ticket->id}", [
                'price' => 150
            ]);

        $response->assertStatus(403);
    }



}
