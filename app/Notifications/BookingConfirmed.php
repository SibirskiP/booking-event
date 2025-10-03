<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Confirmed!')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your booking for '.$this->booking->ticket->type.' ticket is confirmed.')
            ->line('Event: '.$this->booking->ticket->event->title)
            ->line('Thank you for using our application!');
    }

    public function getBooking()
    {
        return $this->booking;
    }
}
