<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_delete_threads()
    {
        $thread = factory('App\Thread')->create();

        $this->delete($thread->path())->assertRedirect('/login');
        
        $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    }

    public function test_an_unauthorized_user_cannot_delete_threads()
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->create();

        $this->delete($thread->path())->assertStatus(403);

        $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    }

    public function test_an_authorized_user_can_delete_threads()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);
        $reply = factory('App\Reply')->create(['thread_id' => $thread->id]);

        $this->delete($thread->path());

        $this->assertDatabaseMissing('threads', $thread->toArray());
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, Activity::count());
    }
}
