<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentioned_users_in_a_reply_are_notified()
    {
        $john = factory('App\User')->create(['name' => 'JohnDoe']);
        $jane = factory('App\User')->create(['name' => 'JaneDoe']);
        $this->be($john);
        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make(['content' => 'Hey @JaneDoe check this out.']);
        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }
}
