<x-guest-layout>
    <div class="container mx-auto py-8">
        <a href="{{ route('events.index') }}" class="text-blue-500 hover:underline mb-4 inline-block">Back to Events</a>
        @if (!request('booking_time'))
            <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->getSummary() }}</h1>

            <div class="mb-4">
                <form action="{{ route('bookings.create', $event->id) }}" method="GET">
                    <label for="time_zone" class="block font-medium text-gray-700 mt-4">Select Time Zone:</label>
                    <select name="time_zone" id="time_zone" class="border rounded p-2" required>
                        @foreach (timezone_identifiers_list() as $timeZone)
                            <option value="{{ $timeZone }}" {{ $timeZone == request('time_zone', 'UTC') ? 'selected' : '' }}>{{ $timeZone }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">Change Time Zone</button>
                </form>
            </div>

            @php
                $selectedTimeZone = request('time_zone', 'UTC');
                $eventDate = \Carbon\Carbon::parse($event->getStart()->getDateTime())->setTimezone($selectedTimeZone)->format('F j, Y H:i');
            @endphp

            <h2 class="text-lg mb-4"><strong>Event Date:</strong> {{ $eventDate }} ({{ $selectedTimeZone }})</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($timeSlots as $time)
                    @php
                        $isBooked = collect(session('bookings', []))->contains(function ($booking) use ($selectedDate, $time) {
                            return $booking['booking_date'] === $selectedDate && $booking['booking_time'] === $time['time'];
                        });
                    @endphp
                    <div class="border p-4 rounded-lg {{ $isBooked ? 'bg-red-100' : 'bg-green-100' }}">
                        <span class="text-lg font-medium">{{ $time['time'] }}</span>
                        @if ($isBooked)
                            <p class="text-red-600">Already booked</p>
                        @else
                            <form action="{{ route('bookings.create', $event->id) }}" method="GET" class="mt-2">
                                <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                                <input type="hidden" name="booking_time" value="{{ $time['time'] }}">
                                <input type="hidden" name="time_zone" value="{{ $selectedTimeZone }}">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded">Select</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-8 p-4 bg-white border rounded-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc list-inside text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('bookings.store', $event->id) }}" method="POST">
                    @csrf
                    <p><strong>Event:</strong> {{ $event->name }}</p>
                    <p><strong>Date:</strong> {{ request('booking_date') }}</p>
                    <p><strong>Time:</strong> {{ request('booking_time') }}</p>
                    <p><strong>Time Zone:</strong> {{ request('time_zone', 'UTC') }}</p>
                    <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
                    <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">
                    <input type="hidden" name="time_zone" value="{{ request('time_zone', 'UTC') }}">

                    <label for="attendee_name">Name:</label>
                    <input type="text" name="attendee_name" id="attendee_name" value="{{ old('attendee_name') }}" required>

                    <label for="attendee_email">Email:</label>
                    <input type="email" name="attendee_email" id="attendee_email" value="{{ old('attendee_email') }}" required>

                    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Confirm Booking</button>
                </form>
            </div>
        @endif
    </div>
</x-guest-layout>
