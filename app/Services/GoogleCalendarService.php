<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        $this->service = new Google_Service_Calendar($this->client);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getService()
    {
        return $this->service;
    }

    public function listEvents($calendarId = 'primary')
    {
        $events = $this->service->events->listEvents($calendarId);
        return $events->getItems();
    }

    public function createEvent($calendarId = 'primary', $eventData)
    {
        $event = new \Google_Service_Calendar_Event($eventData);
        $event = $this->service->events->insert($calendarId, $event);
        return $event;
    }

    public function getEvent($calendarId, $eventId)
    {
        $service = new \Google_Service_Calendar($this->client);
        return $service->events->get($calendarId, $eventId);
    }
}
