<!DOCTYPE html>
<html>
<head>
    <title>Event Details</title>
</head>
<body>
<h1>Event Details</h1>
<p><strong>Summary:</strong> {{ $event->getSummary() }}</p>
<p><strong>ID:</strong> {{ $event->getId() }}</p>
<p><strong>Start:</strong> {{ optional($event->getStart())->getDateTime() ?? 'No start time' }}</p>
<p><strong>End:</strong> {{ optional($event->getEnd())->getDateTime() ?? 'No end time' }}</p>
<p><strong>Description:</strong> {{ $event->getDescription() ?? 'No description' }}</p>
<a href="/google-calendar/events">Back to Events</a>
</body>
</html>
