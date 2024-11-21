<x-guest-layout>
    <div class="flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 max-w-lg w-full">
            <h1 class="text-3xl font-bold mb-4 text-center text-green-600">Thank You!</h1>
            <p class="text-lg mb-4 text-gray-700 text-center">
                Your booking has been successfully created.
            </p>
            <div class="mb-6">
                <p class="text-gray-600 font-semibold">Booking Details:</p>
                <ul class="list-disc pl-5">
                    <li><strong>Event Name:</strong> {{ $booking['event_name'] }}</li>
                    <li><strong>Date:</strong> {{ $booking['booking_date'] }}</li>
                    <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking['booking_time'])->format('H:i') }}</li>
                    <li><strong>Time Zone:</strong> {{ $booking['time_zone'] }}</li>
                </ul>
            </div>
            <div class="text-center">
                <a href="{{ route('events.index') }}"
                   class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Events
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
