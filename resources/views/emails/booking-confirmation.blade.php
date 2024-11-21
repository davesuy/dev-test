<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $booking['attendee_name'] }},</p>
    <p>Thank you for your booking. Here are the details:</p>
    <ul>
        <li><strong>Event Name:</strong> {{ $booking['event_name'] }}</li>
        <li><strong>Date:</strong> {{ $booking['booking_date'] }}</li>
        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking['booking_time'])->format('H:i') }}</li>
        <li><strong>Time Zone:</strong> {{ $booking['time_zone'] }}</li>
    </ul>
    <p>Attached is the calendar invite for your booking.</p>
    <p>Best regards,<br>Your Company</p>
</body>
</html>
