<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $chats = ChatRoom::get(['user_id', 'message', 'created_at']);

        return view('home', compact('chats'));
    }
}
