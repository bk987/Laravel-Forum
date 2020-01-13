<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_has_a_profile()
    {
        $user = factory('App\User')->create();

        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    public function test_profiles_display_all_threads_created_by_the_user()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->get('/profiles/' . $user->name)
            ->assertSee($thread->title)
            ->assertSee($thread->content);
    }
}
