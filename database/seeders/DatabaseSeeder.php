<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 2 Admins
        User::factory()->count(2)->create(['role' => 'admin']);

        // 3 Organizers
        $organizers = User::factory()->count(3)->create(['role' => 'organizer']);

        // 10 Customers
        $customers = User::factory()->count(10)->create(['role' => 'customer']);

        // 5 Events (distribuirati među organizatorima)
        $events = collect();
        foreach ($organizers as $index => $org) {
            // za raspodjelu: svaki organizator kreira 1 ili 2 eventa
            $count = ($index === 0) ? 2 : 1;
            for ($i = 0; $i < $count; $i++) {
                $events->push(Event::factory()->create([
                    'created_by' => $org->id
                ]));
            }
        }
        // ako nemamo 5, kreiraj dodatne
        while ($events->count() < 5) {
            $org = $organizers->random();
            $events->push(Event::factory()->create(['created_by' => $org->id]));
        }

        // 15 Tickets for events
        $tickets = collect();
        foreach ($events as $event) {
            // svaki event 2-4 karte
            $num = rand(2,4);
            for ($i = 0; $i < $num; $i++) {
                $tickets->push(Ticket::factory()->create([
                    'event_id' => $event->id
                ]));
            }
        }

        // 20 Bookings
        $bookings = collect();
        for ($i = 0; $i < 20; $i++) {
            $ticket = $tickets->random();
            $user = $customers->random();
            $quantity = rand(1, min(4, $ticket->quantity));

            $booking = Booking::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'quantity' => $quantity,
                'status' => (rand(1,100) <= 70) ? 'confirmed' : 'pending' // većina potvrđena
            ]);

            // smanji količinu ticketa
            $ticket->decrement('quantity', $quantity);

            // Napravi mocked Payment za confirmed booking
            if ($booking->status === 'confirmed') {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $ticket->price * $quantity,
                    'status' => 'success',
                ]);
            } else {
                // opcionalno: kreiraj failed payment za pending
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $ticket->price * $quantity,
                    'status' => 'failed',
                ]);
            }

            $bookings->push($booking);
        }
    }
}
