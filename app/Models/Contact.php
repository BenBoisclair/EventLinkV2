<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasUuids;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_RESPONDED = 'responded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_READ,
            self::STATUS_RESPONDED,
        ];
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeResponded($query)
    {
        return $query->where('status', self::STATUS_RESPONDED);
    }
}
