<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;

class PreventDoubleBooking
{
    public function handle(Request $request, Closure $next)
    {
        $ticket_id = $request->route('ticket')->id;
        $user_id = auth()->id();

        $exists = Booking::where('ticket_id', $ticket_id)
            ->where('user_id', $user_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You already booked this ticket'], 400);
        }

        return $next($request);
    }
}
