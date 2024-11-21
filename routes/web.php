<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;

use App\Services\GoogleCalendarService;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/google-calendar/auth', function (GoogleCalendarService $googleCalendarService) {
    $client = $googleCalendarService->getClient();
    $authUrl = $client->createAuthUrl();
    return redirect($authUrl);
});

Route::get('/google-calendar/callback', function (GoogleCalendarService $googleCalendarService) {
    $client = $googleCalendarService->getClient();
    $code = request('code');

    if (!$code) {
        return redirect('/google-calendar/auth')->withErrors('Authorization code is missing.');
    }

    try {
        $client->authenticate($code);
        session(['google_calendar_token' => json_encode($client->getAccessToken())]);
        return redirect('/google-calendar/events');
    } catch (\Exception $e) {
        return redirect('/google-calendar/auth')->withErrors('Invalid authorization code.');
    }
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

Route::get('/events/{event}/calendar', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/bookings/thank-you', function () {
    $booking = session('booking');
    if (!$booking) {
        return redirect()->route('events.index')->withErrors('Booking not found.');
    }
    return view('bookings.thank-you', compact('booking'));
})->name('bookings.thank-you');

require __DIR__ . '/auth.php';
