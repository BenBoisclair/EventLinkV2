<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return Inertia::render('Dashboard', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $currentEventId = session('current_event_id');
        $currentEvent = null;

        $events = collect();

        if ($user->currentTeam) {
            $events = $user->currentTeam->events()->orderBy('created_at', 'desc')->get();
            if ($currentEventId) {
                $currentEvent = $events->firstWhere('id', $currentEventId);
                if ($currentEvent) {
                    $currentEvent = \App\Models\Event::with(['website', 'attendees', 'exhibitors'])->find($currentEvent->id);
                }
            }
        }

        return Inertia::render('Dashboard', [
            'event' => $currentEvent,
            'events' => $events,
            'website' => $currentEvent?->website ?? null,
            'attendees' => $currentEvent?->attendees ?? collect(),
            'exhibitors' => $currentEvent?->exhibitors ?? collect(),
        ]);
    })->name('dashboard');

    Route::get('/organiser/events', [\App\Http\Controllers\EventController::class, 'index'])->name('organiser.events');
    Route::get('/organiser/events/create', [\App\Http\Controllers\EventController::class, 'create'])->name('organiser.events.create');
    Route::get('/organiser/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('organiser.events.show');
    Route::get('/organiser/events/{event}/select', [\App\Http\Controllers\EventController::class, 'select'])->name('organiser.events.select');

    Route::get('/builder/{website:slug}', [\App\Http\Controllers\WebsiteController::class, 'builder'])->name('website.builder');

    Route::post('/events/{event}/website/create', [\App\Http\Controllers\WebsiteController::class, 'create'])
        ->name('website.create');

    Route::patch('/events/{event}/website/{website}/meta', [\App\Http\Controllers\WebsiteController::class, 'updateMeta'])
        ->name('website.meta.update');

    Route::post('/events/{event}/website/{website}/favicon', [\App\Http\Controllers\WebsiteController::class, 'updateFavicon'])
        ->name('website.favicon.update');

    Route::post('/organiser/events', [\App\Http\Controllers\EventController::class, 'store'])->name('organiser.events.store');
    Route::patch('/organiser/events/{event}', [\App\Http\Controllers\EventController::class, 'update'])->name('organiser.event.update');

    Route::post('/events/{event}/website/{website}/save', [\App\Http\Controllers\WebsiteController::class, 'save'])
        ->name('website.builder.save');


    Route::post('/events/{event}/website/{website}/publish', [\App\Http\Controllers\WebsiteController::class, 'publish'])
        ->name('website.builder.publish');

    Route::post('/events/{event}/website/{website}/unpublish', [\App\Http\Controllers\WebsiteController::class, 'unpublish'])
        ->name('website.builder.unpublish');

    Route::delete('/organiser/events/{event}/website/{website}', [\App\Http\Controllers\WebsiteController::class, 'destroy'])
        ->name('organiser.event.website.destroy');

    // Attendee management routes
    Route::post('/organiser/events/{event}/attendees', [\App\Http\Controllers\AttendeeController::class, 'store'])
        ->name('organiser.event.attendees.store');
    
    Route::patch('/organiser/events/{event}/attendees/{attendee}', [\App\Http\Controllers\AttendeeController::class, 'update'])
        ->name('organiser.event.attendees.update');
    
    Route::delete('/organiser/events/{event}/attendees/{attendee}', [\App\Http\Controllers\AttendeeController::class, 'destroy'])
        ->name('organiser.event.attendees.destroy');

    // Exhibitor management routes
    Route::post('/organiser/events/{event}/exhibitors', [\App\Http\Controllers\ExhibitorController::class, 'store'])
        ->name('organiser.event.exhibitors.store');
    
    Route::patch('/organiser/events/{event}/exhibitors/{exhibitor}', [\App\Http\Controllers\ExhibitorController::class, 'update'])
        ->name('organiser.event.exhibitors.update');
    
    Route::delete('/organiser/events/{event}/exhibitors/{exhibitor}', [\App\Http\Controllers\ExhibitorController::class, 'destroy'])
        ->name('organiser.event.exhibitors.destroy');

});

Route::get('/site/{website:slug}', [\App\Http\Controllers\WebsiteController::class, 'showPublic'])->name('website.public');

// Public event registration
Route::post('/events/{event}/attendees/register', [\App\Http\Controllers\AttendeeController::class, 'registerForEvent'])
    ->name('attendees.register');
