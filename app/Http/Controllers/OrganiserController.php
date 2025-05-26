<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Route;

class OrganiserController extends Controller
{
    use AuthorizesRequests;

    public function editEvent(Event $event): Response
    {
        $this->authorize('view', $event);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $currentRouteName = Route::currentRouteName();

        $activeSubPage = 'website';

        if ($currentRouteName === 'organiser.event.settings') {
            $activeSubPage = 'settings';
        }

        $event->load('website');

        return Inertia::render('Dashboard', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'start_date' => $event->start_date?->format('Y-m-d'),
                'end_date' => $event->end_date?->format('Y-m-d'),
                'location' => $event->location,
                'contact_phone' => $event->contact_phone,
                'contact_email' => $event->contact_email,
                'website' => $event->website,
                'attendees' => $event->relationLoaded('attendees') ? $event->attendees : null,
                'registrationForm' => $event->relationLoaded('registrationForm') ? $event->registrationForm : null,
            ],
            'events' => $user->organizedEvents()->select(['id', 'name'])->orderBy('created_at', 'desc')->get(),
            'activeSubPage' => $activeSubPage,
        ]);
    }
}
