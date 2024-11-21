<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $bookings = Booking::with('event')->get();
        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        return $this->bookingService->createBooking($request);
    }

    public function create($eventId)
    {
        $data = $this->bookingService->createBookingView($eventId);
        return view('bookings.calendar', $data);
    }
}
