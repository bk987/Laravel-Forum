<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;

class ProfilesController extends Controller
{
    /**
     * Display the specified user's profile and activity feed.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => Activity::feed($user)
        ]);
    }
}
