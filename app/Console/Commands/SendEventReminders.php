<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminderMail;

class SendEventReminders extends Command
{
    protected $signature = 'send:event-reminders';
    protected $description = 'Send event reminders 1 hour before the meeting time';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

        $bookings = Booking::whereBetween('booking_date', [$now, $oneHourLater])->get();

        foreach ($bookings as $booking) {
            Mail::to($booking->attendee_email)->send(new EventReminderMail($booking));
        }

        $this->info('Event reminders sent successfully.');
    }
}
