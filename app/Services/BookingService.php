<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'time_zone' => 'required|string',
        ]);

        $attendeeName = $request->input('attendee_name');
        $attendeeEmail = $request->input('attendee_email');
        $bookingDate = $request->input('booking_date');
        $bookingTime = $request->input('booking_time');
        $timeZone = $request->input('time_zone');

        $bookings = collect(session('bookings', []));

        $existingBooking = $bookings->first(function ($booking) use ($bookingDate, $bookingTime) {
            return $booking['booking_date'] === $bookingDate && $booking['booking_time'] === $bookingTime;
        });

        if ($existingBooking) {
            return redirect()->back()->withErrors('A booking already exists for the selected date and time.');
        }

        $client = $this->googleCalendarService->getClient();
        $client->setAccessToken(session('google_calendar_token'));

        $startDateTime = Carbon::parse("$bookingDate $bookingTime", $timeZone)->toIso8601String();
        $endDateTime = Carbon::parse("$bookingDate $bookingTime", $timeZone)->addMinutes(30)->toIso8601String();

        $eventData = [
            'summary' => "Booking for $attendeeName",
            'description' => "Booking by $attendeeName ($attendeeEmail)",
            'start' => ['dateTime' => $startDateTime, 'timeZone' => $timeZone],
            'end' => ['dateTime' => $endDateTime, 'timeZone' => $timeZone],
        ];

        $event = $this->googleCalendarService->createEvent('primary', $eventData);

        $booking = [
            'event_id' => $event->id,
            'attendee_name' => $attendeeName,
            'attendee_email' => $attendeeEmail,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'time_zone' => $timeZone,
            'event_name' => $event->getSummary(),
        ];

        session()->put('booking', $booking);

        // Send confirmation email
        Mail::to($attendeeEmail)->send(new BookingConfirmationMail($booking));

        return redirect()->route('bookings.thank-you');
    }

    public function generateTimeSlots($date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->startOfDay()->addHours(24);
        $interval = 30;

        $timeSlots = [];

        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);

            $timeSlots[] = [
                'time' => $startOfDay->format('H:i'),
            ];

            $startOfDay = $end;
        }

        return $timeSlots;
    }

    public function createBookingView($eventId)
    {
        $client = $this->googleCalendarService->getClient();
        $client->setAccessToken(json_decode(session('google_calendar_token'), true));
        $event = $this->googleCalendarService->getEvent('primary', $eventId);

        $selectedDate = Carbon::parse($event->getStart()->getDateTime())->toDateString();
        $timeSlots = $this->generateTimeSlots($selectedDate);

        return compact('event', 'timeSlots', 'selectedDate');
    }

}
