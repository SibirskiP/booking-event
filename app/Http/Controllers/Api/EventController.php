<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    use AuthorizesRequests;
    // Lista svih evenata (svi korisnici)
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $date = $request->input('date');

        $cacheKey = "events_page_{$page}_search_{$search}_date_{$date}";

        $events = Cache::remember($cacheKey, 60, function() use ($search, $date) {
            return Event::searchByTitle($search)
                ->filterByDate($date)
                ->paginate(10);
        });

        return response()->json($events);
    }

    // Detalji jednog eventa
    public function show(Event $event)
    {
        $event->load('tickets');
        return response()->json(['data' => $event]);
    }

    // Kreiranje eventa (samo admin/organizer)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        $data['created_by'] = $request->user()->id;

        $event = Event::create($data);

        return response()->json(['data' => $event], 201);
    }

    // Update eventa (samo owner ili admin – policy)
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string|max:255',
        ]);

        $event->update($data);

        return response()->json(['data' => $event]);
    }

    // Brisanje eventa
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();
        return response()->noContent(); // ovo vraća 204

    }
}
