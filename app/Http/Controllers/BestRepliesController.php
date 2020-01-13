<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;

class BestRepliesController extends Controller
{
    /**
     * Mark the best reply for a thread.
     *
     * @param Thread $thread
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread, Reply $reply)
    {
        $this->authorize('update', $thread);

        $thread->markBestReply($reply);
    }
}
