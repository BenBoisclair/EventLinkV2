<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\Website;
use App\Models\Attendee;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Team $team
 * @property Website $website
 * @property Attendee[] $attendees
 * @property Exhibitor[] $exhibitors
 * @property RegistrationForm[] $registrationForms
 */

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'logo_path',
        'banner_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function website()
    {
        return $this->hasOne(Website::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function exhibitors()
    {
        return $this->belongsToMany(Exhibitor::class);
    }

}
