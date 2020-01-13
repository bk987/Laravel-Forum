<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;

class FavoritesController extends Controller
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
     * Store a new favorite in the database.
     *
     * @param Thread $thread
     * @param Reply $reply
     * @return void
     */
    public function store(Thread $thread, Reply $reply)
    {
        $reply->favorite();
    }

    /**
     * Remove the specified favorite from the database.
     *
     * @param Thread $thread
     * @param Reply $reply
     * @return void
     */
    public function destroy(Thread $thread, Reply $reply)
    {
        $reply->unfavorite();
    }
}
