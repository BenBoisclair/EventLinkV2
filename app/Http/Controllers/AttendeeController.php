<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Actions\Attendee\RegisterAttendee;
use Illuminate\Http\RedirectResponse;
use App\Models\Attendee;

class AttendeeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the attendees for a specific event.
     */
    public function index(Event $event): Response
    {
        $this->authorize('view', $event);

        $attendees = $event->attendees()->get();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Dashboard', [
            'event' => $event->load(['website', 'attendees']),
            'attendees' => $attendees,
            'events' => $user->organizedEvents()->orderBy('created_at', 'desc')->get(),
            'activeSubPage' => 'attendees',
        ]);
    }

    /**
     * Register an attendee for an event from the public event website.
     */
    public function registerForEvent(Request $request, Event $event, RegisterAttendee $registerAttendee): RedirectResponse
    {
        // Check if the event has a published website
        if (!$event->website || !$event->website->is_published) {
            return back()->withErrors(['message' => 'Event registration is not available.']);
        }

        // Get form fields from the specific AttendeesForm block
        $blockId = $request->input('_block_id');
        $attendeeFormBlock = null;
        
        if ($event->website && $blockId) {
            // Find the specific block by ID
            $attendeeFormBlock = $event->website->blocks()
                ->where('id', $blockId)
                ->where('type', 'AttendeesForm')
                ->first();
        } elseif ($event->website) {
            // Fallback: use the first AttendeesForm block if no ID provided
            $attendeeFormBlock = $event->website->blocks()
                ->where('type', 'AttendeesForm')
                ->first();
        }
        
        if (!$attendeeFormBlock) {
            return back()->withErrors(['message' => 'No registration form is available for this event.']);
        }
        
        $fields = $attendeeFormBlock->content['props']['fields'] ?? [];

        // Build validation rules based on the form configuration
        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('attendees')->where(function ($query) use ($event) {
                return $query->where('event_id', $event->id);
            })],
        ];

        // Filter enabled fields only
        $fields = array_filter($fields, function($field) {
            return ($field['enabled'] ?? true) !== false;
        });
        foreach ($fields as $field) {
            if ($field['required'] ?? false) {
                $fieldRules = ['required'];
                
                if ($field['type'] === 'email') {
                    $fieldRules[] = 'email';
                } elseif (in_array($field['type'], ['text', 'textarea'])) {
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                }
                
                $rules[$field['name']] = $fieldRules;
            } else {
                $rules[$field['name']] = ['nullable', 'string', 'max:255'];
            }
        }

        $validated = $request->validate($rules);

        // Separate standard fields from custom fields
        $standardFields = [
            'first_name' => $validated['first_name'] ?? '',
            'last_name' => $validated['last_name'] ?? '',
            'email' => $validated['email'],
        ];

        $customFields = array_diff_key($validated, $standardFields);

        try {
            $registerAttendee->handle($event, $standardFields, $customFields);
            
            return back()->with('success', 'Thank you for registering! You will receive a confirmation email shortly.');
        } catch (\Exception $e) {
            Log::error("Error registering attendee for event {$event->id}: {$e->getMessage()}");
            return back()->withErrors(['message' => 'An error occurred during registration. Please try again.']);
        }
    }

    /**
     * Store a newly created attendee in storage.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('attendees')->where(function ($query) use ($event) {
                    return $query->where('event_id', $event->id);
                })
            ],
        ]);

        $event->attendees()->create($validated);

        return redirect()->back()->with('success', 'Attendee added successfully.');
    }

    /**
     * Update the specified attendee in storage.
     */
    public function update(Request $request, Event $event, Attendee $attendee): RedirectResponse
    {
        $this->authorize('update', $event);

        // Ensure the attendee belongs to the event
        if ($attendee->event_id !== $event->id) {
            abort(404);
        }


        // Check if email is being changed
        $emailRule = ['required', 'email', 'max:255'];
        
        // Only check uniqueness if email is being changed
        if ($request->input('email') !== $attendee->email) {
            $emailRule[] = Rule::unique('attendees')
                ->where('event_id', $event->id);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => $emailRule,
        ]);

        $attendee->update($validated);

        return redirect()->back()->with('success', 'Attendee updated successfully.');
    }

    /**
     * Remove the specified attendee from storage.
     */
    public function destroy(Event $event, Attendee $attendee): RedirectResponse
    {
        $this->authorize('update', $event);

        // Ensure the attendee belongs to the event
        if ($attendee->event_id !== $event->id) {
            abort(404);
        }

        $attendee->delete();

        return redirect()->back()->with('success', 'Attendee removed successfully.');
    }

}
