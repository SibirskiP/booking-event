<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    /** @test */
    public function it_can_create_a_payment_for_a_booking()
    {
        $booking = Booking::factory()->create();

        $payment = $this->service->createPayment($booking, 100);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 100,
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_fails_to_create_payment_if_amount_is_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $booking = Booking::factory()->create();

        $this->service->createPayment($booking, 0); // nevalidan iznos
    }

    /** @test */
    public function it_can_mark_payment_as_confirmed()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $this->service->markAsConfirmed($payment);

        $this->assertEquals('confirmed', $payment->fresh()->status);
    }

    /** @test */
    public function it_can_mark_payment_as_cancelled()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $this->service->markAsCancelled($payment);

        $this->assertEquals('cancelled', $payment->fresh()->status);
    }

    /** @test */
    public function it_can_check_if_payment_is_pending()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);
        $this->assertTrue($this->service->isPending($payment));

        $payment->update(['status' => 'confirmed']);
        $this->assertFalse($this->service->isPending($payment));
    }

    /** @test */
    public function it_can_check_if_payment_is_confirmed()
    {
        $payment = Payment::factory()->create(['status' => 'confirmed']);
        $this->assertTrue($this->service->isConfirmed($payment));

        $payment->update(['status' => 'cancelled']);
        $this->assertFalse($this->service->isConfirmed($payment));
    }
}
