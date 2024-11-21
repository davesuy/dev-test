<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index(GoogleCalendarService $googleCalendarService)
    {
        $client = $googleCalendarService->getClient();
        $client->setAccessToken(json_decode(session('google_calendar_token'), true));

        $events = $googleCalendarService->listEvents();
        $now = new \DateTime();

        $futureEvents = array_filter($events, function ($event) use ($now) {
            $start = $event->getStart();
            if ($start && $start->getDateTime()) {
                $eventDate = new \DateTime($start->getDateTime());
                return $eventDate >= $now;
            }
            return false;
        });

        return view('events.index', ['events' => $futureEvents]);
    }

}
