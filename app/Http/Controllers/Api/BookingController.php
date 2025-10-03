<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\BookingConfirmed;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    // Pregled svih booking-a za current user
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()->with('ticket.event')->get();
        return response()->json(['data' => $bookings]);
    }

    // Kreiranje booking-a (samo customers)
    public function store(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $ticket->quantity],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'quantity' => $request->quantity
        ]);

        return response()->json($booking, 201);
    }



    public function pay(Booking $booking, PaymentService $paymentService)
    {
        if ($booking->user_id != auth()->id()) return response()->json(['message'=>'Forbidden'],403);

        $payment = $paymentService->pay($booking);
        $booking->user->notify(new BookingConfirmed($booking));
        return response()->json(['status'=>'success','data'=>$payment],201);
    }

    public function destroy(Booking $booking)
    {
        $user = auth()->user();

        // provjera vlasništva
        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $booking->delete();

        return response()->noContent(); // ovo vraća 204
    }


}
