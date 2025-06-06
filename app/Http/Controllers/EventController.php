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
        $teamEventData = null;

        if ($user->currentTeam) {
            $events = $user->currentTeam->events()->orderBy('created_at', 'desc')->get();
            $teamEventData = [
                'event_count' => $user->currentTeam->eventCount(),
                'event_limit' => $user->currentTeam->getEventLimit(),
                'can_create_event' => $user->currentTeam->canCreateEvent(),
                'remaining_slots' => $user->currentTeam->remainingEventSlots(),
                'has_unlimited_events' => $user->currentTeam->hasUnlimitedEvents(),
                'plan' => $user->currentTeam->plan ? [
                    'name' => $user->currentTeam->plan->name,
                    'slug' => $user->currentTeam->plan->slug,
                ] : null,
                'billing_cycle' => $user->currentTeam->billing_cycle ?? 'monthly',
            ];
        }

        return Inertia::render('Events/Index', [
            'events' => $events,
            'team_event_data' => $teamEventData
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
        
        // Check if the team can create more events
        if (!$user->currentTeam || !$user->currentTeam->canCreateEvent()) {
            $limitText = $user->currentTeam->hasUnlimitedEvents() 
                ? 'unlimited' 
                : $user->currentTeam->getEventLimit();
                
            return redirect()->back()->withErrors([
                'event_limit' => 'You have reached the maximum number of events allowed for your team. (Limit: ' . $limitText . ')'
            ]);
        }
        
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

    /**
     * Delete the specified event.
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();
        
        // Ensure the event belongs to the user's current team
        if (!$user->currentTeam || $event->team_id !== $user->currentTeam->id) {
            abort(403, 'Unauthorized');
        }

        // Clear current event from session if it's the one being deleted
        if (session('current_event_id') == $event->id) {
            session()->forget('current_event_id');
        }

        $event->delete();

        return redirect()->route('organiser.events')->with('success', 'Event deleted successfully!');
    }
}
