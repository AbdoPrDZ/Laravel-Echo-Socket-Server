<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->post('/broadcasting/client_connect', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'You are connected to the broadcasting server.',
        'all' => $request->all(),
    ]);
});

Route::middleware('auth:sanctum')->post('/broadcasting/client_connect', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'You are disconnected from broadcasting server.',
        'all' => $request->all(),
    ]);
});

Broadcast::channel('messages', function ($user) {
    // event(new App\Events\Message\MessageLoadEvent(App\Models\Message::all()->toArray()));
    $messages = [];
    foreach (App\Models\Message::all() as $message) {
        $messages[$message->id] = $message;
    }
    return [
        'success' => true,
        'message' => 'Successfully subscription to messages channel.',
        // 'messages' => $messages,
    ];
});
