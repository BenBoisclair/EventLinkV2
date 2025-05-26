<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organiser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'logo_path',
    ];

    /**
     * Get the user that owns the organiser.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
