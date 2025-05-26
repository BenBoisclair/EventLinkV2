<?php

namespace App\Jobs;

use App\Events\ExhibitorImageProcessed; // New Event
use App\Models\Exhibitor; // Use Exhibitor model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File; // Keep this
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UploadExhibitorImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // S3 Base directory is now dynamic via constructor
    // private const S3_DIRECTORY = 'exhibitor/logo'; // Removed

    public int $timeout = 300;
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param int $eventId The ID of the event context for this upload.
     * @param int $exhibitorId The ID of the exhibitor record.
     * @param string $tempPath The path to the temporary file on the local disk.
     * @param string $originalFilename The original name of the uploaded file.
     * @param string $fieldName The database field to update ('logo_path' or 'banner_path').
     * @param string $s3Directory The target directory within the S3 bucket (e.g., 'exhibitor/logo').
     * @param string|null $existingPath The path of the existing file on S3 to be replaced (optional).
     */
    public function __construct(
        public int $eventId,
        public int $exhibitorId,
        public string $tempPath, // Path relative to storage/app
        public string $originalFilename,
        public string $fieldName, // 'logo_path' or 'banner_path'
        public string $s3Directory, // 'exhibitor/logo' or 'exhibitor/banner'
        public ?string $existingPath = null // Optional existing path
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting UploadExhibitorImage job", [
            'event_id' => $this->eventId,
            'exhibitor_id' => $this->exhibitorId,
            'field' => $this->fieldName,
            'temp_path' => $this->tempPath,
            's3_dir' => $this->s3Directory
        ]);

        $localDisk = Storage::disk('local');
        $fullTempPath = $localDisk->path($this->tempPath); // Get absolute path for Http\\File

        if (!$localDisk->exists($this->tempPath)) {
            Log::error("Temporary file {$this->tempPath} not found for exhibitor {$this->exhibitorId}, field {$this->fieldName}. Aborting job.");
            return; // Nothing to clean up if file doesn't exist
        }

        try {
            $exhibitor = Exhibitor::find($this->exhibitorId);

            if (!$exhibitor) {
                Log::warning("Exhibitor {$this->exhibitorId} not found. Aborting upload job for field {$this->fieldName}.");
                $this->cleanupTempFile();
                return;
            }

            // Delete old file from S3 if an existing path was provided
            if ($this->existingPath) {
                 Log::info("Existing image path '{$this->existingPath}' provided for exhibitor {$this->exhibitorId}, field {$this->fieldName}. Attempting deletion from S3.");
                try {
                    if (Storage::disk('s3')->exists($this->existingPath)) { // Check if it actually exists before deleting
                        Storage::disk('s3')->delete($this->existingPath);
                        Log::info("Successfully deleted old image '{$this->existingPath}' from S3.");
                    } else {
                         Log::info("Old image '{$this->existingPath}' not found on S3. Skipping deletion.");
                    }
                } catch (Throwable $e) {
                    Log::error("Failed to delete old S3 image '{$this->existingPath}' for exhibitor {$this->exhibitorId}, field {$this->fieldName}: {$e->getMessage()}", ['exception' => $e]);
                    // Continue with upload even if deletion failed
                }
            }

            $s3Path = $this->uploadToS3($fullTempPath);
            Log::info("Successfully uploaded image for exhibitor {$this->exhibitorId}, field {$this->fieldName} to path: {$s3Path}");

            $this->updateExhibitorRecord($exhibitor, $s3Path);
            Log::info("Exhibitor {$this->exhibitorId} database record updated for field {$this->fieldName}.");

            // Get public URL for broadcasting
            $s3Url = Storage::url($s3Path);
            $this->broadcastUpdate($s3Url);

            $this->cleanupTempFile();

            Log::info("Finished UploadExhibitorImage job successfully", [
                'event_id' => $this->eventId,
                'exhibitor_id' => $this->exhibitorId,
                'field' => $this->fieldName
            ]);

        } catch (Throwable $e) {
            Log::error("Exception in UploadExhibitorImage job", [
                'event_id' => $this->eventId,
                'exhibitor_id' => $this->exhibitorId,
                'field' => $this->fieldName,
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            $this->cleanupTempFile();
            $this->fail($e); // Let Laravel handle retry/failure logic
        }
    }

    /**
     * Uploads the temporary file to S3 and returns the S3 storage path.
     *
     * @throws \Exception if the upload fails.
     */
    private function uploadToS3(string $fullTempPath): string
    {
        $extension = pathinfo($this->originalFilename, PATHINFO_EXTENSION);
        $filenameWithoutExt = pathinfo($this->originalFilename, PATHINFO_FILENAME);
        $safeFilename = Str::slug($filenameWithoutExt);

        // Use the desired naming convention: {exhibitor_id}-{slugged_original_filename}.{ext}
        $s3Filename = $this->exhibitorId . '-' . $safeFilename . '.' . $extension;
        $s3FullPath = $this->s3Directory . '/' . $s3Filename; // Combine directory and filename

        Log::info("Attempting S3 upload", [
            'event_id' => $this->eventId,
            'exhibitor_id' => $this->exhibitorId,
            'field' => $this->fieldName,
            'source_temp_path' => $this->tempPath, // Log relative path for consistency
            's3_target_path' => $s3FullPath
        ]);

        $uploadSuccess = Storage::disk('s3')->putFileAs(
            $this->s3Directory,    // Directory path on S3
            new File($fullTempPath), // Use Illuminate\\Http\\File with absolute path
            $s3Filename             // Filename on S3
        );

        if (!$uploadSuccess) {
            Log::error("Failed to upload file to S3", [
                'event_id' => $this->eventId,
                'exhibitor_id' => $this->exhibitorId,
                'field' => $this->fieldName,
                's3_target_path' => $s3FullPath
            ]);
            throw new \Exception("S3 upload failed for exhibitor {$this->exhibitorId}, field {$this->fieldName}");
        }

         Log::info("S3 upload successful", [
             'event_id' => $this->eventId,
             'exhibitor_id' => $this->exhibitorId,
             'field' => $this->fieldName,
             's3_path' => $s3FullPath
         ]);

        return $s3FullPath; // Return the path relative to the disk root
    }

    /**
     * Updates the exhibitor's specific field with the new image S3 path.
     */
    private function updateExhibitorRecord(Exhibitor $exhibitor, string $s3Path): void
    {
        $fieldName = $this->fieldName; // 'logo_path' or 'banner_path'
        $exhibitor->$fieldName = $s3Path;
        $exhibitor->save();
    }

    /**
     * Broadcasts the ExhibitorImageProcessed event.
     */
    private function broadcastUpdate(string $s3Url): void
    {
        try {
            broadcast(new ExhibitorImageProcessed($this->eventId, $this->exhibitorId, $this->fieldName, $s3Url));
            Log::info("Broadcast ExhibitorImageProcessed event", [
                'event_id' => $this->eventId,
                'exhibitor_id' => $this->exhibitorId,
                'field' => $this->fieldName
            ]);
        } catch (Throwable $e) {
             Log::error("Failed to broadcast ExhibitorImageProcessed event", [
                 'event_id' => $this->eventId,
                 'exhibitor_id' => $this->exhibitorId,
                 'field' => $this->fieldName,
                 'message' => $e->getMessage()
             ]);
             // Don't fail the job just because broadcast failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical("UploadExhibitorImage job FAILED permanently after retries", [
            'event_id' => $this->eventId,
            'exhibitor_id' => $this->exhibitorId,
            'field' => $this->fieldName,
            'temp_path' => $this->tempPath,
            'message' => $exception->getMessage()
        ]);
        $this->cleanupTempFile();
        // TODO: Optionally, notify user/admin about the permanent failure.
    }

    /**
     * Helper function to delete the temporary file from local storage.
     */
    private function cleanupTempFile(): void
    {
        if ($this->tempPath && Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
            Log::info("Cleaned up temporary file {$this->tempPath}", [
                'event_id' => $this->eventId,
                'exhibitor_id' => $this->exhibitorId,
                'field' => $this->fieldName
            ]);
        }
    }
}
