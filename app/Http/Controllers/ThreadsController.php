<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Category;
use App\Filters\ThreadFilters;
use App\Http\Requests\ThreadStoreRequest;
use App\Http\Requests\ThreadUpdateRequest;

class ThreadsController extends Controller
{
    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of threads.
     *
     * @param ThreadFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function index(ThreadFilters $filters)
    {
        $threads = $this->getThreads($filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads
        ]);
    }

    /**
     * Show the form for creating a new thread.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create', [
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created thread in database.
     *
     * @param ThreadStoreRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(ThreadStoreRequest $request)
    {
        $thread = Thread::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content
        ]);

        if (request()->wantsJson()) {
            return response($thread, 201);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified thread.
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    /**
     * Update the specified thread in database.
     *
     * @param ThreadUpdateRequest $request
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadUpdateRequest $request, Thread $thread)
    {
        $thread->update($request->all());

        return $thread;
    }

    /**
     * Remove the specified thread from database.
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response('', 200);
        }

        session()->flash('success', 'Thread deleted.');
        return redirect(route('threads.index'));
    }

    /**
     * Fetch all relevant threads.
     *
     * @param ThreadFilters $filters
     * @return mixed
     */
    protected function getThreads(ThreadFilters $filters)
    {
        $threads = Thread::filter($filters);

        return $threads->latest()->paginate(25);
    }
}
