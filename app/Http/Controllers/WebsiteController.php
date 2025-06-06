<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\WebsiteSettings;
use App\Models\Event;
use App\Models\Website;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created website.
     */
    public function create(Request $request, Event $event)
    {
        if ($event->website()->exists()) {
            return response()->json(['error' => 'A website already exists for this event.'], 409);
        }

        try {
            $defaultSettings = WebsiteSettings::fromDefaults($event)->toArray();
            $website = $event->website()->create([
                'settings' => $defaultSettings,
                'is_published' => false,
                'slug' => Str::slug($event->name),
            ]);
            return redirect()->route('dashboard', [
                'event' => $event->id,
                'website' => $website->id,
            ])->with('success', 'Website created successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to create website for event {$event->id}: {$e->getMessage()}");
            return redirect()->route('dashboard', [
                'event' => $event->id,
            ])->with('error', 'Failed to create website. Please try again.');
        }
    }

    /**
     * Show the website builder page.
     */
    public function builder(Website $website)
    {
        $event = $website->event;
        $website->load(['blocks' => function($query) {
            $query->orderBy('order');
        }]);

        return Inertia::render('WebsiteBuilder/Builder', [
            'event' => $event,
            'websiteId' => $website->id,
            'websiteSlug' => $website->slug,
            'blocks' => $website->blocks->map(function($block) {
                return [
                    'id' => (string) $block->id,
                    'type' => $block->type,
                    'props' => $block->content['props'] ?? [],
                ];
            })->values(),
            'websiteSettings' => $website->settings,
            'isPublished' => $website->is_published,
            'lastUpdatedAt' => $website->updated_at?->toISOString(),
            'faviconUrl' => $website->favicon_url,
        ]);
    }


    /**
     * Display the specified public website using the website slug.
     */
    public function showPublic(Website $website): InertiaResponse
    {
        if (!$website->is_published) {
            abort(404, 'Website not found.');
        }

        $website->load(['blocks' => function($query) {
            $query->orderBy('order');
        }]);

        $websiteData = [
            'id' => (string)$website->id,
            'settings' => $website->settings,
            'blocks' => $website->blocks->map(function ($block) {
                return [
                    'id' => (string) $block->id,
                    'type' => $block->type,
                    'props' => $block->content['props'] ?? [],
                ];
            }),
            'event' => $website->event ? [
                'id' => (string)$website->event->id,
                'name' => $website->event->name,
                'location' => $website->event->location,
                'start_date' => $website->event->start_date?->toIso8601String(),
                'end_date' => $website->event->end_date?->toIso8601String(),
            ] : null,
            'is_published' => $website->is_published,
            'favicon_url' => $website->favicon_url,
        ];

        return Inertia::render('Public/WebsiteView', [
            'website' => $websiteData
        ]);
    }

    /**
     * Preview the website.
     */
    public function preview(Event $event)
    {
        $website = $event->website()->with(['blocks' => function($query) {
            $query->orderBy('order');
        }])->first();

        if (!$website) {
            abort(404, 'Website not found for this event.');
        }

        return Inertia::render('Organiser/Website/Preview', [
            'event' => [
                'id' => (string) $event->id,
                'name' => $event->name,
                'location' => $event->location,
                'start_date' => $event->start_date?->toIso8601String(),
                'end_date' => $event->end_date?->toIso8601String(),
            ],
            'website' => [
                'id' => (string) $website->id,
                'is_published' => $website->is_published,
                'settings' => $website->settings,
                'blocks' => $website->blocks->map(function ($block) {
                    return [
                        'id' => (string) $block->id,
                        'type' => $block->type,
                        'props' => $block->content['props'] ?? [],
                    ];
                }),
            ],
        ]);
    }

    /**
     * Update the meta information for the specified website.
     */
    public function updateMeta(Request $request, Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }
        $validated = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ]);
        $settings = $website->settings;
        $settings->metadata->title = $validated['meta_title'] ?? '';
        $settings->metadata->description = $validated['meta_description'] ?? '';
        $website->settings = $settings;
        $website->save();

        return redirect()->route('dashboard', [
            'event' => $event->id,
            'website' => $website->id,
        ])->with('success', 'Meta information updated!');
    }

    /**
     * Update the favicon for the specified website.
     */
    public function updateFavicon(Request $request, Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }

        $validated = $request->validate([
            'favicon' => 'required|image|mimes:png,ico,jpg,jpeg|max:2048',
        ]);

        if ($request->hasFile('favicon')) {
            $faviconFile = $request->file('favicon');

            // Delete old favicon if exists
            if ($website->favicon_path && Storage::disk('s3')->exists($website->favicon_path)) {
                Storage::disk('s3')->delete($website->favicon_path);
            }

            $extension = $faviconFile->getClientOriginalExtension();
            $timestamp = time();
            $hash = substr(md5($faviconFile->getClientOriginalName() . $timestamp), 0, 8);
            $path = "favicons/{$website->id}/{$timestamp}-{$hash}.{$extension}";

            Storage::disk('s3')->putFileAs(
                dirname($path),
                $faviconFile,
                basename($path),
                [
                    'ContentType' => $faviconFile->getMimeType(),
                    'CacheControl' => 'public, max-age=31536000', // Cache for 1 year
                ]
            );

            $website->favicon_path = $path;
            $website->save();
        }

        $website->refresh();

        return redirect()->route('dashboard', [
            'event' => $event->id,
            'website' => $website->id,
        ])->with('success', 'Favicon updated!')
           ->with('faviconUrl', $website->favicon_url);
    }

    /**
     * Save the website blocks.
     */
    public function save(Request $request, Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }

        $validated = $this->validateSaveRequest($request);

        $incomingBlocks = $validated['blocks'] ?? [];
        $this->deleteRemovedBlocks($website, $incomingBlocks);

        foreach ($incomingBlocks as $index => $blockData) {
            $processedBlockData = $this->preprocessBlockData($blockData, $index, $request);
            $block = $this->createOrUpdateBlock($website, $processedBlockData, $index);
            $this->processPendingFiles($index, $request, $website, $block);
        }

        // Handle theme data
        if (isset($validated['theme']) && is_array($validated['theme'])) {
            $this->updateWebsiteTheme($website, $validated['theme']);
        }

        // Handle styling data
        if (isset($validated['styling']) && is_array($validated['styling'])) {
            $this->updateWebsiteStyling($website, $validated['styling']);
        }

        return $this->buildSaveResponse($website);
    }

    /**
     * Publish the website.
     */
    public function publish(Request $request, Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }

        $website->is_published = true;
        $website->save();

        return response()->json([
            'success' => true,
            'message' => 'Website published successfully!',
            'is_published' => $website->is_published,
            'published_at' => $website->updated_at->toISOString(),
        ]);
    }

    public function unpublish(Request $request, Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }

        $website->is_published = false;
        $website->save();

        return response()->json([
            'success' => true,
            'message' => 'Website unpublished successfully!',
            'is_published' => $website->is_published,
            'unpublished_at' => $website->updated_at->toISOString(),
        ]);

    }


    /**
     * Delete the specified website.
     */
    public function destroy(Event $event, Website $website)
    {
        if ($website->event_id !== $event->id) {
            abort(403, 'Website does not belong to this event.');
        }

        // Delete all blocks associated with the website
        $website->blocks()->delete();

        // Delete the website
        $website->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Website deleted successfully!');
    }

    /**
     * Preprocess request data to convert string booleans to actual booleans.
     */
    private function preprocessRequestForValidation(Request $request): void
    {
        $allInput = $request->all();
        
        if (isset($allInput['blocks']) && is_array($allInput['blocks'])) {
            foreach ($allInput['blocks'] as $index => $block) {
                if (isset($block['props']) && is_array($block['props'])) {
                    $allInput['blocks'][$index]['props'] = $this->convertStringBooleans($block['props']);
                }
            }
            
            $request->replace($allInput);
        }
    }

    /**
     * Validate the save request with dynamic file validation.
     */
    private function validateSaveRequest(Request $request): array
    {
        // Convert string booleans BEFORE validation for FormData requests
        $this->preprocessRequestForValidation($request);

        try {
            $validated = $request->validate([
                'blocks' => 'present|array',
            'blocks.*.id' => 'required_with:blocks.*|string',
            'blocks.*.type' => 'required_with:blocks.*|string',
            'blocks.*.props' => 'nullable|array',
            // Base block properties
            'blocks.*.props.backgroundColor' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'blocks.*.props.useThemeBackground' => 'nullable|boolean',
            'blocks.*.props.title' => 'nullable|string',
            // AttendeesForm block specific properties
            'blocks.*.props.fields' => 'nullable|array',
            'blocks.*.props.fields.*.name' => 'nullable|string',
            'blocks.*.props.fields.*.label' => 'nullable|string',
            'blocks.*.props.fields.*.type' => 'nullable|string',
            'blocks.*.props.fields.*.required' => 'nullable|boolean',
            'blocks.*.props.fields.*.enabled' => 'nullable|boolean',
            'blocks.*.props.fields.*.deletable' => 'nullable|boolean',
            'blocks.*.props.fields.*.options' => 'nullable|array',
            'blocks.*.props.buttonText' => 'nullable|string',
            // Hero block specific properties
            'blocks.*.props.imageUrl' => 'nullable|string',
            'blocks.*.props.altText' => 'nullable|string',
            'blocks.*.props.headingText' => 'nullable|string',
            'blocks.*.props.descriptionText' => 'nullable|string',
            'blocks.*.props.textPosition' => 'nullable|string',
            'blocks.*.props.overlayEnabled' => 'nullable|boolean',
            // Description block specific properties
            'blocks.*.props.description' => 'nullable|string',
            // Countdown block specific properties
            'blocks.*.props.startDate' => 'nullable|string',
            'blocks.*.props.endDate' => 'nullable|string',
            'blocks.*.props.useEventDates' => 'nullable|boolean',
            'blocks.*.props.showDays' => 'nullable|boolean',
            'blocks.*.props.showHours' => 'nullable|boolean',
            'blocks.*.props.showMinutes' => 'nullable|boolean',
            'blocks.*.props.showSeconds' => 'nullable|boolean',
            'blocks.*.props.finishedText' => 'nullable|string',
            'blocks.*.props.buttonLink' => 'nullable|string',
            'blocks.*.props.buttonEnabled' => 'nullable|boolean',
            // Stats block specific properties
            'blocks.*.props.stats' => 'nullable|array',
            'blocks.*.props.stats.*.title' => 'nullable|string',
            'blocks.*.props.stats.*.value' => 'nullable|string',
            // Canvas block specific properties
            'blocks.*.props.canvasData' => 'nullable|string',
            // Icon properties
            'blocks.*.props.descriptionIcon' => 'nullable|string',
            // Additional common properties that might be missing
            'blocks.*.props.websiteId' => 'nullable',
            'blocks.*.props.event' => 'nullable',
            'blocks.*.props.id' => 'nullable|string',
            'blocks.*.props.device' => 'nullable|string',
            'theme' => 'nullable|array',
            'theme.primary' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.secondary' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.accent' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme.background' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'styling' => 'nullable|array',
            'styling.borderRadius' => 'nullable|string',
            'styling.buttonSize' => 'nullable|string',
            'styling.shadow' => 'nullable|string',
            'styling.buttonStyle' => 'nullable|string',
            'styling.animationSpeed' => 'nullable|string',
            'styling.fontWeight' => 'nullable|string',
            'styling.letterSpacing' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        // Dynamically validate any pending file fields
        $dynamicRules = [];
        foreach ($request->input('blocks', []) as $index => $block) {
            if (isset($block['props']) && is_array($block['props'])) {
                foreach (array_keys($block['props']) as $propName) {
                    if (str_starts_with($propName, '_pendingFile_')) {
                        $dynamicRules["blocks.{$index}.props.{$propName}"] = 'required|file|image|mimes:png,jpg,jpeg|max:10240';
                    }
                }
            }
        }

        if (!empty($dynamicRules)) {
            $request->validate($dynamicRules);
        }

        return $validated;
    }

    /**
     * Delete blocks that are no longer in the incoming blocks array.
     */
    private function deleteRemovedBlocks(Website $website, array $incomingBlocks): void
    {
        $incomingBlockIds = collect($incomingBlocks)->pluck('id')->filter()->all();

        if (!empty($incomingBlockIds)) {
            // Filter to only numeric IDs since only persisted blocks have numeric database IDs
            // New blocks have string IDs like "Stats-1749033341694" and don't exist in DB yet
            $numericIds = collect($incomingBlockIds)->filter(function($id) {
                return is_numeric($id);
            })->all();
            
            if (!empty($numericIds)) {
                $website->blocks()->whereNotIn('id', $numericIds)->delete();
            } else {
                // If no existing blocks are being kept, delete all existing blocks
                $website->blocks()->delete();
            }
        } else {
            $website->blocks()->delete();
        }
    }

    /**
     * Preprocess block data to handle pending files and set upload flags.
     */
    private function preprocessBlockData(array $blockData, int $index, Request $request): array
    {
        $props = $blockData['props'] ?? [];

        // Mark files as uploading and remove pending file entries
        foreach (array_keys($props) as $propName) {
            if (str_starts_with($propName, '_pendingFile_')) {
                $targetPropName = str_replace('_pendingFile_', '', $propName);
                $fileKey = "blocks.{$index}.props.{$propName}";

                if ($request->hasFile($fileKey)) {
                    $props["_{$targetPropName}_uploadingToS3"] = true;
                    unset($props[$propName]);
                }
            }
        }

        $blockData['props'] = $props;
        return $blockData;
    }

    /**
     * Create or update a block with the given data.
     */
    private function createOrUpdateBlock(Website $website, array $blockData, int $index): \App\Models\Block
    {
        // Convert boolean strings back to actual booleans and parse JSON strings
        $originalProps = $blockData['props'] ?? [];
        if ($blockData['type'] === 'AttendeesForm') {
            Log::info("AttendeesForm block received props: " . json_encode($originalProps));
        }
        
        $props = $this->convertStringBooleans($originalProps);
        
        if ($blockData['type'] === 'AttendeesForm') {
            Log::info("AttendeesForm block converted props: " . json_encode($props));
        }

        if (!empty($blockData['id']) && is_numeric($blockData['id'])) {
            return $website->blocks()->updateOrCreate(
                ['id' => $blockData['id']],
                [
                    'name' => $blockData['type'] . '_' . $blockData['id'],
                    'type' => $blockData['type'],
                    'order' => $index,
                    'content' => [
                        'props' => $props,
                    ],
                ]
            );
        } else {
            // New block - create it
            return $website->blocks()->create([
                'name' => $blockData['type'] . '_' . uniqid(),
                'type' => $blockData['type'],
                'order' => $index,
                'content' => [
                    'props' => $props,
                ],
            ]);
        }
    }

    /**
     * Process pending file uploads for a block.
     */
    private function processPendingFiles(int $index, Request $request, Website $website, \App\Models\Block $block): void
    {
        // Use the original request data before preprocessing to extract pending files
        $originalBlockData = $request->input("blocks.{$index}", []);
        $pendingFiles = $this->extractPendingFiles($originalBlockData, $index, $request);

        foreach ($pendingFiles as $targetPropName => $fileInfo) {
            $imageFile = $fileInfo['file'];

            // Store temporarily on local disk
            $extension = $imageFile->getClientOriginalExtension();
            $randomString = substr(md5(uniqid()), 0, 8);
            $tempFileName = "block-{$block->id}-{$targetPropName}-{$randomString}.{$extension}";
            $tempPath = $imageFile->storeAs('temp-uploads', $tempFileName, 'local');

            // Generate final S3 path using the actual block ID
            $finalS3Path = "blocks/{$website->id}/{$block->id}/{$randomString}-" . time() . ".{$extension}";

            // Dispatch the upload job with the actual block ID
            \App\Jobs\UploadBlockImage::dispatch(
                $website->id,
                (string)$block->id,
                $tempPath,
                $finalS3Path,
                $targetPropName
            );

            Log::info("Queued UploadBlockImage job for block {$block->id}, prop {$targetPropName}");
        }
    }

    /**
     * Extract pending files from block data.
     */
    private function extractPendingFiles(array $blockData, int $index, Request $request): array
    {
        $pendingFiles = [];
        $props = $blockData['props'] ?? [];

        foreach (array_keys($props) as $propName) {
            if (str_starts_with($propName, '_pendingFile_')) {
                $targetPropName = str_replace('_pendingFile_', '', $propName);
                $fileKey = "blocks.{$index}.props.{$propName}";

                if ($request->hasFile($fileKey)) {
                    $pendingFiles[$targetPropName] = [
                        'file' => $request->file($fileKey),
                        'fileKey' => $fileKey,
                    ];
                }
            }
        }

        return $pendingFiles;
    }

    /**
     * Convert string boolean values to actual booleans and parse JSON-stringified objects/arrays.
     * Preserves file uploads and other special values.
     */
    private function convertStringBooleans(array $props): array
    {
        foreach ($props as $key => $value) {
            // Skip file uploads and pending file markers
            if (str_starts_with($key, '_pendingFile_') || $value instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }
            
            if (is_string($value)) {
                // Handle boolean strings
                if ($value === 'true' || $value === 'false') {
                    $props[$key] = $value === 'true';
                }
                // Handle JSON-stringified arrays/objects
                elseif (str_starts_with($value, '[') || str_starts_with($value, '{')) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $props[$key] = $decoded;
                    }
                }
            }
        }

        return $props;
    }

    /**
     * Update the website theme in settings.
     */
    private function updateWebsiteTheme(Website $website, array $themeData): void
    {
        $settings = $website->settings;
        
        // Update theme properties if provided
        if (isset($themeData['primary'])) {
            $settings->theme->primary = $themeData['primary'];
        }
        if (isset($themeData['secondary'])) {
            $settings->theme->secondary = $themeData['secondary'];
        }
        if (isset($themeData['accent'])) {
            $settings->theme->accent = $themeData['accent'];
        }
        if (isset($themeData['background'])) {
            $settings->theme->background = $themeData['background'];
        }
        
        $website->settings = $settings;
        $website->save();
    }

    /**
     * Update the website styling in settings.
     */
    private function updateWebsiteStyling(Website $website, array $stylingData): void
    {
        $settings = $website->settings;
        
        // Update styling properties if provided
        if (isset($stylingData['borderRadius'])) {
            $settings->styling->borderRadius = $stylingData['borderRadius'];
        }
        if (isset($stylingData['buttonSize'])) {
            $settings->styling->buttonSize = $stylingData['buttonSize'];
        }
        if (isset($stylingData['shadow'])) {
            $settings->styling->shadow = $stylingData['shadow'];
        }
        if (isset($stylingData['buttonStyle'])) {
            $settings->styling->buttonStyle = $stylingData['buttonStyle'];
        }
        if (isset($stylingData['animationSpeed'])) {
            $settings->styling->animationSpeed = $stylingData['animationSpeed'];
        }
        if (isset($stylingData['fontWeight'])) {
            $settings->styling->fontWeight = $stylingData['fontWeight'];
        }
        if (isset($stylingData['letterSpacing'])) {
            $settings->styling->letterSpacing = $stylingData['letterSpacing'];
        }
        
        $website->settings = $settings;
        $website->save();
    }

    /**
     * Build the response for the save operation.
     */
    private function buildSaveResponse(Website $website)
    {
        $website->touch();

        // Reload blocks to get the latest state (especially useful without WebSocket)
        $website->load(['blocks' => function($query) {
            $query->orderBy('order');
        }]);

        // Return updated blocks data
        return back()->with('success', 'Website saved successfully!')
                     ->with('blocks', $website->blocks->map(function($block) {
                         return [
                             'id' => (string) $block->id,
                             'type' => $block->type,
                             'props' => $block->content['props'] ?? [],
                         ];
                     })->values());
    }
}
