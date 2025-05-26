<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->belongsToTeam($event->team);
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->belongsToTeam($event->team) && 
               ($user->ownsTeam($event->team) || $user->hasTeamPermission($event->team, 'update'));
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->belongsToTeam($event->team) && 
               ($user->ownsTeam($event->team) || $user->hasTeamPermission($event->team, 'delete'));
    }
}