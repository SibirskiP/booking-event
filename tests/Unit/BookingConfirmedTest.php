<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Notification;

class BookingConfirmedTest extends TestCase
{
    /** @test */
    public function it_sends_booking_confirmed_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $user->notify(new BookingConfirmed($booking));

        Notification::assertSentTo(
            [$user],
            BookingConfirmed::class,
            function ($notification, $channels) use ($booking) {
                return $notification->getBooking()->id === $booking->id;
            }
        );

    }
}
