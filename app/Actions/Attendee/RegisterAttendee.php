<?php

namespace App\Actions\Attendee;

use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterAttendee
{
    public function handle(Event $event, array $validated, array $customFields = []): Attendee
    {
        try {
            $attendee = $event->attendees()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'custom_fields' => $customFields,
            ]);

            return $attendee;
        } catch (ValidationException $e) {
            Log::warning("Validation failed during attendee registration for event {$event->id}", ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("Failed to register attendee for event {$event->id}: {$e->getMessage()}", [
                'exception' => $e,
                'validatedData' => $validated,
                'customFields' => $customFields
            ]);
            throw new \Exception('Failed to register attendee due to a server error.');
        }
    }
}
