<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Website;
use Illuminate\Support\Facades\Storage;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'name',
        'type',
        'content',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Get the content attribute with image URLs transformed.
     */
    public function getContentAttribute($value)
    {
        $content = is_string($value) ? json_decode($value, true) : $value;
        
        if (!isset($content['props']) || !is_array($content['props'])) {
            return $content;
        }
        
        // Transform any image paths to full URLs
        foreach ($content['props'] as $key => $propValue) {
            if (is_string($propValue) && $this->isImagePath($propValue)) {
                // Convert path to full URL
                $content['props'][$key] = $this->getImageUrl($propValue);
            }
        }
        
        return $content;
    }
    
    /**
     * Check if a value is an image path.
     */
    private function isImagePath(string $value): bool
    {
        // Check if it starts with 'blocks/' and has an image extension
        return preg_match('/^blocks\/\d+\/[^\/]+\/[^\/]+\.(jpg|jpeg|png|gif|webp)$/i', $value);
    }
    
    /**
     * Get the raw content without transformations.
     */
    public function getRawContent()
    {
        return $this->attributes['content'] ?? null;
    }
    
    /**
     * Convert an image path to a full URL.
     */
    private function getImageUrl(string $path): string
    {
        // Try S3 first
        if (config('filesystems.default') === 's3' || Storage::disk('s3')->exists($path)) {
            return config('filesystems.disks.s3.url') . '/' . $path;
        }
        
        // Fallback to public storage
        return asset('storage/' . $path);
    }
}
