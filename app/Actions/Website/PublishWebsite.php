<?php

declare(strict_types=1);

namespace App\Actions\Website;

use App\Models\Website;
use Illuminate\Support\Facades\Log;

class PublishWebsite
{
    public function handle(Website $website): bool
    {
        try {
            if (!$website->is_published) {
                $website->is_published = true;
                $saved = $website->save();
                Log::info("Website {$website->id} published successfully.");
                return $saved;
            }
            Log::info("Website {$website->id} was already published.");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to publish website {$website->id}: {$e->getMessage()}", ['exception' => $e]);
            return false;
        }
    }
}
