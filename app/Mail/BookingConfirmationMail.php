<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $icsContent = $this->generateIcsContent();

        return $this->view('emails.booking-confirmation')
                    ->subject('Booking Confirmation')
                    ->attachData($icsContent, 'booking.ics', [
                        'mime' => 'text/calendar',
                    ]);
    }

    private function generateIcsContent()
    {
        $startDateTime = \Carbon\Carbon::parse("{$this->booking['booking_date']} {$this->booking['booking_time']}", $this->booking['time_zone'])->toIso8601String();
        $endDateTime = \Carbon\Carbon::parse("{$this->booking['booking_date']} {$this->booking['booking_time']}", $this->booking['time_zone'])->addMinutes(30)->toIso8601String();

        return "BEGIN:VCALENDAR
        VERSION:2.0
        PRODID:-//Your Company//NONSGML v1.0//EN
        BEGIN:VEVENT
        UID:" . uniqid() . "
        DTSTAMP:" . now()->format('Ymd\THis\Z') . "
        DTSTART:{$startDateTime}
        DTEND:{$endDateTime}
        SUMMARY:Booking for {$this->booking['attendee_name']}
        DESCRIPTION:Booking by {$this->booking['attendee_name']} ({$this->booking['attendee_email']})
        END:VEVENT
        END:VCALENDAR";
    }
}
