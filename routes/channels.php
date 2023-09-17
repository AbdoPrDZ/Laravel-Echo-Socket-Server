<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('messages', function ($user) {
    // event(new App\Events\Message\MessageLoadEvent(App\Models\Message::all()->toArray()));
    foreach (App\Models\Message::all() as $message) {
        $messages[$message->id] = $message;
    }
    return $messages;
});
