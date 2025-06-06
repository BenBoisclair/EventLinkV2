<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Website;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Website channel authorization
Broadcast::channel('website.{websiteId}', function (User $user, $websiteId) {
    $website = Website::find($websiteId);
    
    if (!$website) {
        return false;
    }
    
    // Check if the user belongs to the team that owns the event
    $event = $website->event;
    
    if (!$event) {
        return false;
    }
    
    // Check if the user is a member of the team that owns the event
    return $user->belongsToTeam($event->team);
});