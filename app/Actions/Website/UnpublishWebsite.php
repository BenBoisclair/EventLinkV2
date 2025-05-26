<?php

declare(strict_types=1);

namespace App\Actions\Website;

use App\Models\Website;
use Illuminate\Support\Facades\Log;

class UnpublishWebsite
{
    public function handle(Website $website): bool
    {
        try {
            if ($website->is_published) {
                $website->is_published = false;
                $saved = $website->save();
                Log::info("Website {$website->id} unpublished successfully.");
                return $saved;
            }
            Log::info("Website {$website->id} was already unpublished.");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to unpublish website {$website->id}: {$e->getMessage()}", ['exception' => $e]);
            return false;
        }
    }
}
