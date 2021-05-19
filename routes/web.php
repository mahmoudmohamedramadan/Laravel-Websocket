<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('chat_room', function() {
    ChatRoom::create([
        'user_id' => request()->values['userId'],
        'message' => request()->values['msg'],
    ]);
});
