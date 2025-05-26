<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Exhibitor;
use Illuminate\Http\RedirectResponse;
use App\Jobs\UploadExhibitorImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExhibitorController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle exhibitor image upload with proper parameters for the job.
     */
    private function uploadExhibitorImage(
        $file,
        Event $event,
        Exhibitor $exhibitor,
        string $fieldName,
        string $directory
    ): void {
        if (!$file) return;

        try {
            // Create temp directory if it doesn't exist
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Generate unique filename
            $tempPath = 'temp/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Use streams for memory efficiency
            $stream = fopen($file->getRealPath(), 'r');
            Storage::disk('local')->put($tempPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            
            // Dispatch job to handle S3 upload and database update
            dispatch(new UploadExhibitorImage(
                $event->id,
                $exhibitor->id,
                $tempPath,
                $file->getClientOriginalName(),
                $fieldName,
                $directory,
                $exhibitor->$fieldName
            ));
        } catch (\Exception $e) {
            // Log error but don't fail the entire request
            Log::error('Failed to process exhibitor image upload', [
                'error' => $e->getMessage(),
                'exhibitor_id' => $exhibitor->id,
                'field' => $fieldName
            ]);
        }
    }

    /**
     * Display a listing of the exhibitors for a specific event.
     */
    public function index(Event $event): Response
    {
        $this->authorize('view', $event);

        $exhibitors = $event->exhibitors()->get();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Dashboard', [
            'event' => $event->load(['website', 'exhibitors']),
            'exhibitors' => $exhibitors,
            'events' => $user->organizedEvents()->orderBy('created_at', 'desc')->get(),
            'activeSubPage' => 'exhibitors',
        ]);
    }

    /**
     * Store a new exhibitor for the event.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:exhibitors,email',
            'contact_phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
        ]);

        // Get current user for the exhibitor
        $user = Auth::user();

        // Create exhibitor with proper field mapping
        $exhibitor = Exhibitor::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['contact_email'], // Map contact_email to email
            'phone' => $validated['contact_phone'], // Map contact_phone to phone
            'description' => $validated['description'] ?? null,
        ]);

        // Attach to event
        $event->exhibitors()->attach($exhibitor->id);

        // Handle file uploads
        $this->uploadExhibitorImage(
            $request->file('logo'),
            $event,
            $exhibitor,
            'logo_path',
            'exhibitor/logo'
        );

        $this->uploadExhibitorImage(
            $request->file('banner'),
            $event,
            $exhibitor,
            'banner_path',
            'exhibitor/banner'
        );

        return redirect()->back()->with('success', 'Exhibitor created successfully');
    }

    /**
     * Update an existing exhibitor.
     */
    public function update(Request $request, Event $event, Exhibitor $exhibitor): RedirectResponse
    {
        $this->authorize('update', $event);

        // Ensure the exhibitor belongs to this event
        if (!$event->exhibitors()->where('exhibitors.id', $exhibitor->id)->exists()) {
            abort(404, 'Exhibitor not found for this event');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email|unique:exhibitors,email,' . $exhibitor->id,
            'contact_phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
        ]);

        // Update with proper field mapping
        $updateData = [];
        if (isset($validated['name'])) $updateData['name'] = $validated['name'];
        if (isset($validated['contact_email'])) $updateData['email'] = $validated['contact_email'];
        if (isset($validated['contact_phone'])) $updateData['phone'] = $validated['contact_phone'];
        if (isset($validated['description'])) $updateData['description'] = $validated['description'];

        $exhibitor->update($updateData);

        // Handle file uploads
        $this->uploadExhibitorImage(
            $request->file('logo'),
            $event,
            $exhibitor,
            'logo_path',
            'exhibitor/logo'
        );

        $this->uploadExhibitorImage(
            $request->file('banner'),
            $event,
            $exhibitor,
            'banner_path',
            'exhibitor/banner'
        );

        return redirect()->back()->with('success', 'Exhibitor updated successfully');
    }

    /**
     * Remove an exhibitor from the event.
     */
    public function destroy(Event $event, Exhibitor $exhibitor): RedirectResponse
    {
        $this->authorize('update', $event);

        // Ensure the exhibitor belongs to this event
        if (!$event->exhibitors()->where('exhibitors.id', $exhibitor->id)->exists()) {
            abort(404, 'Exhibitor not found for this event');
        }

        // Detach from event (don't delete the exhibitor itself)
        $event->exhibitors()->detach($exhibitor->id);

        return redirect()->back()->with('success', 'Exhibitor removed from event');
    }
}
