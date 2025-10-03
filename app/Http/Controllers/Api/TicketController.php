<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TicketController extends Controller
{
    use AuthorizesRequests; // <-- dodano
    // Lista ticketa za određeni event
    public function index(Event $event)
    {
        $tickets = $event->tickets;
        return response()->json(['data' => $tickets]);
    }

    // Kreiranje ticketa (admin/organizer i owner eventa)
    public function store(Request $request, Event $event)
    {
        $this->authorize('create', [Ticket::class, $event]);

        $data = $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $data['event_id'] = $event->id;

        $ticket = Ticket::create($data);

        return response()->json(['data' => $ticket], 201);
    }

    // Update ticketa
    public function update(Request $request, Event $event, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'type' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $ticket->update($data);

        return response()->json(['data' => $ticket]);
    }

    // Brisanje ticketa
    public function destroy(Event $event, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted']);
    }
}
