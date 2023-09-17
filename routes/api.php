<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('create_user', function() {
    $user = User::create([
        'name' => 'Abdo Pr',
        'email' => 'abdopr47@gmail.com',
        'password' => Hash::make('123456'),
    ]);
    return [
        'success' => true,
        'message' => 'successfully creating user',
        'user' => $user,
    ];
});

Route::get('login', function(Request $request) {
    $authed = Auth::attempt(['email' => 'abdopr47@gmail.com', 'password' => '123456']);
    if ($authed) {
    $user = $request->user();
    $token = $user->createToken('access.token', ['remember'])->plainTextToken;
        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
        ];
    }
    return [
        'success' => false,
    ];
});

Route::get('message', function() {
    $messages = Message::all();
    return [
        'success' => true,
        'messages' => $messages,
    ];
});

Route::get('message/create', function() {
    $message = Message::create([
        'title' => 'message title ' . Carbon::now(),
        'content' => 'message message ' . Carbon::now(),
    ]);
    return [
        'success' => true,
        'message' => $message,
    ];
});

Route::get('message/{id}/update', function($id) {
    $message = Message::find($id);
    if(is_null($message)) return "abort(404), id: '$id', $message";
    $message->title = 'message title u-' . Carbon::now();
    $message->content = 'message message u-' . Carbon::now();
    $message->save();
    return [
        'success' => true,
        'message' => $message,
    ];
});

Route::get('message/{id}/delete', function($id) {
    $message = Message::find($id);
    if(is_null($message)) return "abort(404), id: $id";
    $message->delete();
    return [
        'success' => true,
        'id' => $id,
    ];
});
