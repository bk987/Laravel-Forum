<?php

namespace App\Http\Controllers;

use App\Thread;

class ThreadSubscriptionsController extends Controller
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
     * Store a new thread subscription.
     *
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Delete an existing thread subscription.
     *
     * @param Thread $thread
     * @return void
     */
    public function destroy(Thread $thread)
    {
        $thread->unsubscribe();
    }
}
