<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_thread_author_can_mark_any_reply_as_the_best_reply()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);
        $replies = factory('App\Reply', 2)->create(['thread_id' => $thread->id]);
        
        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$thread->slug, $replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }
    
    public function test_only_the_thread_author_can_mark_a_reply_as_best()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);
        $replies = factory('App\Reply', 2)->create(['thread_id' => $thread->id]);

        $this->be(factory('App\User')->create());

        $this->postJson(route('best-replies.store', [$thread->slug, $replies[1]->id]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }
    
    public function test_a_thread_is_updated_when_the_best_reply_is_deleted()
    {
        $this->be($user = factory('App\User')->create());
        $reply = factory('App\Reply')->create(['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', [$reply->thread->slug, $reply]));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
