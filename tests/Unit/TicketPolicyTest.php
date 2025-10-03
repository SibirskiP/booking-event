<?php

namespace Tests\Unit;

use Tests\TestCase; // Laravel TestCase
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Policies\TicketPolicy;

class TicketPolicyTest extends TestCase
{
    /** @test */
    public function organizer_can_create_ticket_for_own_event()
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['created_by' => $organizer->id]);

        $policy = new TicketPolicy();

        $this->assertTrue($policy->create($organizer, $event));
    }
}

