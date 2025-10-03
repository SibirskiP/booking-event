<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use InvalidArgumentException;

class PaymentService
{
    public function createPayment(Booking $booking, float $amount): Payment
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0");
        }

        return Payment::create([
            'booking_id' => $booking->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }

    public function markAsConfirmed(Payment $payment): void
    {
        $payment->update(['status' => 'confirmed']);
    }

    public function markAsCancelled(Payment $payment): void
    {
        $payment->update(['status' => 'cancelled']);
    }

    public function isPending(Payment $payment): bool
    {
        return $payment->status === 'pending';
    }

    public function isConfirmed(Payment $payment): bool
    {
        return $payment->status === 'confirmed';
    }
}
