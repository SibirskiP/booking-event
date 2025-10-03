<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Mock payment
    public function store(Booking $booking)
    {
        if ($booking->user_id != auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->ticket->price * $booking->quantity,
            'status' => 'success', // uvijek success za mock
        ]);

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json(['status' => 'success', 'data' => $payment], 201);
    }

    public function show(Payment $payment)
    {
        return response()->json(['status' => 'success', 'data' => $payment]);
    }
}
