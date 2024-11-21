<!DOCTYPE html>
<html>
<head>
    <title>Event Reminder</title>
</head>
<body>
    <h1>Event Reminder</h1>
    <p>Dear {{ $booking['attendee_name'] }},</p>
    <p>This is a reminder for your upcoming event:</p>
    <ul>
        <li><strong>Event Name:</strong> {{ $booking['event_name'] }}</li>
        <li><strong>Date:</strong> {{ $booking['booking_date'] }}</li>
        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking['booking_time'])->format('H:i') }}</li>
        <li><strong>Time Zone:</strong> {{ $booking['time_zone'] }}</li>
    </ul>
    <p>Best regards,<br>Your Company</p>
</body>
</html>
