<?php

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['author', 'popular', 'unanswered', 'subscribed'];

    /**
     * Filter the query by a given username.
     *
     * @param  string $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function author($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function popular()
    {
        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter the query according to unanswered threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }

    /**
     * Filter the query according to subscribed threads.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function subscribed()
    {
        return $this->builder
            ->whereHas('subscriptions', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest();
    }
}
