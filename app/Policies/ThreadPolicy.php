<?php

namespace App\Policies;

use App\Thread;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the authenticated user has permission to store a new thread.
     *
     * @param User $user
     * @return mixed
     */
    public function store(User $user)
    {
        return true;
    }

    /**
     * Determine if the authenticated user has permission to update a thread.
     *
     * @param User $user
     * @param Thread $thread
     * @return mixed
     */
    public function update(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }
}
