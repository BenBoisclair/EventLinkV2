<?php

namespace App\Jobs;

use App\Events\BlockImageProcessed;
use App\Models\Block;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadBlockImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param int $websiteId The ID of the website.
     * @param string $blockId The ID of the block.
     * @param string $tempPath The relative path to the temporary file in the 'local' disk.
     * @param string $finalS3Path The final desired path on the 's3' disk.
     * @param string $propName The prop name to set with the image URL.
     */
    public function __construct(
        public int $websiteId,
        public string $blockId,
        public string $tempPath, // Path relative to storage/app
        public string $finalS3Path, // Final path on S3
        public string $propName // The prop name to update with image URL
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting UploadBlockImage job for block {$this->blockId} (prop: {$this->propName})");

        $localDisk = Storage::disk('local');

        if (!$localDisk->exists($this->tempPath)) {
            Log::error("Temporary file {$this->tempPath} not found for block {$this->blockId}. Aborting job.");
            return;
        }

        try {
            $block = Website::findOrFail($this->websiteId)
                ->blocks()
                ->find($this->blockId);

            if (!$block) {
                Log::warning("Block {$this->blockId} not found for website {$this->websiteId}. Aborting upload job.");
                $this->cleanupTempFile();
                return;
            }

            $this->uploadToS3();
            Log::info("Successfully uploaded image for block {$this->blockId} to S3 path: {$this->finalS3Path}");

            $this->updateBlockRecord($block);
            Log::info("Block {$this->blockId} database record updated with {$this->propName}.");

            // Broadcast with the path, not the full URL
            $this->broadcastUpdate($this->finalS3Path);

            $this->cleanupTempFile();

            Log::info("Finished UploadBlockImage job successfully for block {$this->blockId}");

        } catch (Throwable $e) {
            Log::error("Exception in UploadBlockImage job for block {$this->blockId}: {$e->getMessage()}", [
                'exception' => $e
            ]);
            $this->cleanupTempFile();
            $this->fail($e);
        }
    }

    /**
     * Uploads the temporary file to S3 or falls back to public storage.
     *
     * @throws \Exception if all upload methods fail.
     */
    private function uploadToS3(): void
    {
        $localDisk = Storage::disk('local');

        if (!$localDisk->exists($this->tempPath)) {
            throw new \Exception("Temporary file {$this->tempPath} does not exist on local disk.");
        }

        // Try S3 first
        try {
            $s3Disk = Storage::disk('s3');

            $stream = $localDisk->readStream($this->tempPath);

            if ($stream === false) {
                throw new \Exception("Could not read stream from temporary file {$this->tempPath}.");
            }



            // Don't specify visibility - bucket policy handles public access
            $uploadSuccess = $s3Disk->put($this->finalS3Path, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($uploadSuccess) {
                Log::info("S3 upload successful for block {$this->blockId}");
                return;
            }

            Log::warning("S3 upload failed for block {$this->blockId} - uploadSuccess was false, falling back to local storage");

        } catch (\Exception $e) {
            Log::error("S3 upload exception for block {$this->blockId}: {$e->getMessage()}, falling back to local storage. Exception: " . get_class($e));
        }

        // Fallback to public storage
        try {
            $publicDisk = Storage::disk('public');
            $publicPath = "block-images/{$this->websiteId}/{$this->blockId}/{$this->propName}-" . time() . "." . pathinfo($this->tempPath, PATHINFO_EXTENSION);

            $stream = $localDisk->readStream($this->tempPath);
            if ($stream === false) {
                throw new \Exception("Could not read stream from temporary file {$this->tempPath}.");
            }

            $uploadSuccess = $publicDisk->put($publicPath, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if (!$uploadSuccess) {
                throw new \Exception("Public storage upload failed for block {$this->blockId}");
            }

            Log::info("Successfully uploaded to public storage for block {$this->blockId}");
            
            // Update the finalS3Path to use the public path instead
            $this->finalS3Path = $publicPath;
            return;

        } catch (\Exception $e) {
            Log::error("Both S3 and public storage failed for block {$this->blockId}: {$e->getMessage()}");
            throw new \Exception("All upload methods failed for block {$this->blockId}: {$e->getMessage()}");
        }
    }

    /**
     * Updates the block's content with the new image path and saves it.
     */
    private function updateBlockRecord(Block $block): void
    {
        // Get the current content (this will use the accessor which might transform URLs)
        $content = $block->content ?? [];
        $props = $content['props'] ?? [];

        // Save only the path, not the full URL
        $props[$this->propName] = $this->finalS3Path;
        
        // Remove the uploading flag
        unset($props["_{$this->propName}_uploadingToS3"]);

        $content['props'] = $props;
        
        // Use the normal Laravel attribute assignment
        $block->content = $content;
        $block->save();
    }

    /**
     * Broadcasts the BlockImageProcessed event.
     */
    private function broadcastUpdate(string $imageUrl): void
    {
        try {
            broadcast(new BlockImageProcessed($this->websiteId, $this->blockId, $imageUrl, $this->propName));
            Log::info("Broadcast BlockImageProcessed event for block {$this->blockId} (prop: {$this->propName})");
        } catch (Throwable $e) {
             Log::error("Failed to broadcast BlockImageProcessed event for block {$this->blockId}: {$e->getMessage()}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical("UploadBlockImage job FAILED permanently for block {$this->blockId} after retries: {$exception->getMessage()}");
        $this->cleanupTempFile();
        // TODO: Optionally, send a notification to the user that the upload failed.
    }

    /**
     * Helper function to delete the temporary file.
     */
    private function cleanupTempFile(): void
    {
        if (Storage::disk('local')->exists($this->tempPath)) {
            Storage::disk('local')->delete($this->tempPath);
            Log::info("Cleaned up temporary file {$this->tempPath} for block {$this->blockId}");
        }
    }
}
