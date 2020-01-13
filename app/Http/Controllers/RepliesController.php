<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use App\Http\Requests\ReplyStoreRequest;
use App\Http\Requests\ReplyUpdateRequest;

class RepliesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Fetch all relevant replies for the specified thread.
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function index(Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * Store a newly created reply in database.
     *
     * @param ReplyStoreRequest $request
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function store(ReplyStoreRequest $request, Thread $thread)
    {
        if ($thread->locked) {
            return response('Thread is locked.', 422);
        }

        $reply = $thread->addReply([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);

        return $reply->load('author');
    }

    /**
     * Update the specified reply in database.
     *
     * @param ReplyUpdateRequest $request
     * @param Thread $thread
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function update(ReplyUpdateRequest $request, Thread $thread, Reply $reply)
    {
        $reply->update($request->all());

        if (request()->wantsJson()) {
            return $reply;
        }

        session()->flash('success', 'Reply updated.');

        return redirect()->back();
    }

    /**
     * Remove the specified reply from database.
     *
     * @param Thread $thread
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
