<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageRepliesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_add_replies()
    {
        $this->post('/threads/test/replies', [])
            ->assertRedirect('/login');
    }

    public function test_an_authenticated_user_can_add_replies()
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['content' => $reply->content]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    public function test_a_reply_requires_some_content()
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make(['content' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('content');
    }

    public function test_unauthorized_users_cannot_delete_replies()
    {
        $reply = factory('App\Reply')->create();

        $this->delete("/threads/{$reply->thread->slug}/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->be(factory('App\User')->create());

        $this->delete("/threads/{$reply->thread->slug}/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_authorized_users_can_delete_replies()
    {
        $this->be($user = factory('App\User')->create());
        $reply = factory('App\Reply')->create(['user_id' => $user->id]);

        $this->delete("/threads/{$reply->thread->slug}/replies/{$reply->id}")
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    public function test_unauthorized_users_cannot_update_replies()
    {
        $reply = factory('App\Reply')->create();

        $this->patch("/threads/{$reply->thread->slug}/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->be(factory('App\User')->create());
        
        $this->patch("/threads/{$reply->thread->slug}/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_authorized_users_can_update_replies()
    {
        $this->be($user = factory('App\User')->create());

        $reply = factory('App\Reply')->create(['user_id' => $user->id]);

        $updatedReply = 'You been changed.';
        $this->patch("/threads/{$reply->thread->slug}/replies/{$reply->id}", ['content' => $updatedReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'content' => $updatedReply]);
    }

    public function test_replies_that_contain_spam_may_not_be_created()
    {
        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make(['content' => 'xxx']);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    public function test_users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make();

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
