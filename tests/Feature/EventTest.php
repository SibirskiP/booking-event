<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function organizer_can_create_event()
    {
        $user = User::factory()->create(['role' => 'organizer']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/events', [
            'title' => 'Test Event',
            'date' => '2025-12-31',
            'location' => 'Sarajevo'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('events', ['title' => 'Test Event']);
    }

    /** @test */
    public function customer_cannot_create_event()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/events', [
            'title' => 'Forbidden Event',
            'date' => '2025-12-31',
            'location' => 'Sarajevo'
        ]);

        $response->assertStatus(403);
    }


    /** @test */
    public function organizer_can_update_their_event()
    {
        $user = User::factory()->create(['role' => 'organizer']);
        $event = \App\Models\Event::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/events/{$event->id}", [
            'title' => 'Updated Event Title',
            'date' => '2026-01-01',
            'location' => 'Mostar'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title'
        ]);
    }

    /** @test */
    public function customer_cannot_update_event()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = \App\Models\Event::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/events/{$event->id}", [
            'title' => 'Illegal Update'
        ]);

        $response->assertStatus(403);
    }


    /** @test */
    public function organizer_can_delete_their_event()
    {
        $user = User::factory()->create(['role' => 'organizer']);
        $event = \App\Models\Event::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }


    /** @test */
    public function customer_cannot_delete_event()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = \App\Models\Event::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/events/{$event->id}");
        $response->assertStatus(403);
    }


    /** @test */
    /** @test */
    public function can_view_single_event()
    {
        $user = \App\Models\User::factory()->create(); // autentifikacija
        $event = \App\Models\Event::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/events/{$event->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $event->id]);
    }

    /** @test */
    public function can_view_all_events()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Event::factory()->count(3)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/events");
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // ako koristiš pagination/data wrapper
    }



}
