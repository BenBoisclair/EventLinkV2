<?php

declare(strict_types=1);

namespace App\Actions\Website;

use App\Models\Website;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HandleFaviconUpdate
{
    public function handle(Website $website, UploadedFile $faviconFile): ?string
    {
        try {
            DB::beginTransaction();

            $originalFaviconPath = $website->getRawOriginal('favicon');

            if ($originalFaviconPath && Storage::exists($originalFaviconPath)) {
                Storage::delete($originalFaviconPath);
            } else if ($originalFaviconPath) {
                Log::warning("Old favicon path found ({$originalFaviconPath}) but file doesn't exist in storage.");
            }

            $faviconPath = $faviconFile->store('favicons');

            if ($faviconPath === false) {
                Log::error("Failed to store favicon for website {$website->id}. Storage::store returned false.");
                throw new \RuntimeException('Server failed to store the favicon.');
            }

            $website->favicon = $faviconPath;
            $website->save();

            DB::commit();

            return Storage::url($faviconPath);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Favicon update failed for website {$website->id}: {$e->getMessage()}", ['exception' => $e]);
            throw new \Exception("An error occurred while updating the favicon: " . $e->getMessage(), 0, $e);
        }
    }
}
