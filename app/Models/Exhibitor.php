<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhibitor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'description',
        'banner_path',
        'logo_path',
    ];

    /**
     * Get the user that owns the exhibitor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The events that the exhibitor is participating in.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}
