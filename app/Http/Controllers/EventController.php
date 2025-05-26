<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $events = collect();

        if ($user->currentTeam) {
            $events = $user->currentTeam->events()->orderBy('created_at', 'desc')->get();
        }

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }

    public function create()
    {
        return Inertia::render('Events/CreateEvent');
    }

    public function show(Event $event)
    {
        return Inertia::render('Events/ShowEvent', [
            'event' => $event
        ]);
    }

    /**
     * Set the selected event as current in the session and redirect to dashboard.
     */
    public function select(Event $event)
    {
        $user = Auth::user();
        // Ensure the event belongs to the user's current team
        if (!$user->currentTeam || $event->team_id !== $user->currentTeam->id) {
            abort(403, 'Unauthorized');
        }
        session(['current_event_id' => $event->id]);
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'banner_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);
        $event = \App\Models\Event::create($validated);
        return redirect()->route('organiser.events')->with('success', 'Event created successfully!');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        
        // Ensure the event belongs to the user's current team
        if (!$user->currentTeam || $event->team_id !== $user->currentTeam->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event->update($validated);

        return redirect()->back()->with('success', 'Event updated successfully!');
    }
}
