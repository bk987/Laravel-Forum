<?php

namespace App\Http\Controllers;

use App\Thread;

class LockedThreadsController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('administrator');
    }

    /**
     * Lock the specified thread.
     *
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);
    }

    /**
     * Unlock the specified thread.
     *
     * @param Thread $thread
     * @return void
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
