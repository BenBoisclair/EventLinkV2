<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'event_limit',
        'plan_id',
        'billing_cycle',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'event_limit' => 'integer',
        ];
    }

    /**
     * Get the plan that the team belongs to.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the events for the team.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the current number of events for the team.
     */
    public function eventCount(): int
    {
        return $this->events()->count();
    }

    /**
     * Get the effective event limit for the team.
     */
    public function getEventLimit(): int
    {
        if ($this->plan) {
            return $this->plan->getEventLimit();
        }

        return 1;
    }

    /**
     * Check if the team can create more events.
     */
    public function canCreateEvent(): bool
    {
        $limit = $this->getEventLimit();

        // If limit is PHP_INT_MAX (unlimited), always allow
        if ($limit === PHP_INT_MAX) {
            return true;
        }

        return $this->eventCount() < $limit;
    }

    /**
     * Get the remaining event slots for the team.
     */
    public function remainingEventSlots(): int
    {
        $limit = $this->getEventLimit();

        // If unlimited, return a large number for display
        if ($limit === PHP_INT_MAX) {
            return 999;
        }

        return max(0, $limit - $this->eventCount());
    }

    /**
     * Check if the team has unlimited events.
     */
    public function hasUnlimitedEvents(): bool
    {
        return $this->getEventLimit() === PHP_INT_MAX;
    }
}
