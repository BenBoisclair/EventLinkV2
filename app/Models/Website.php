<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Block;
use App\DTOs\WebsiteSettings;

/**
 * @property WebsiteSettings $settings
 */

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'settings',
        'favicon_path',
        'is_published',
    ];

    protected $casts = [
        'settings' => \App\Casts\WebsiteSettingsCast::class,
        'is_published' => 'boolean',
    ];

    protected $appends = ['favicon_url'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function getFaviconUrlAttribute()
    {
        if (!$this->favicon_path) {
            return null;
        }
        $s3Url = config('filesystems.disks.s3.url') ?: env('AWS_URL');
        if (!$s3Url) {
            return null;
        }
        $fullUrl = rtrim($s3Url, '/') . '/' . ltrim($this->favicon_path, '/');
        return $fullUrl;
    }
}
