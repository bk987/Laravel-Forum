<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_favorite_anything()
    {
        $this->post('threads/test/replies/1/favorites')
            ->assertRedirect('/login');
    }

    public function test_an_authenticated_user_can_favorite_any_reply()
    {
        $this->be($user = factory('App\User')->create());
        $reply = factory('App\Reply')->create();

        $this->post("threads/{$reply->thread->slug}/replies/{$reply->id}/favorites");

        $this->assertCount(1, $reply->favorites);
    }

    public function test_an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->be($user = factory('App\User')->create());
        $reply = factory('App\Reply')->create();
        $reply->favorite();

        $this->delete("threads/{$reply->thread->slug}/replies/{$reply->id}/favorites");

        $this->assertCount(0, $reply->favorites);
    }

    public function test_an_authenticated_user_can_only_favorite_a_reply_once()
    {
        $this->be($user = factory('App\User')->create());
        $reply = factory('App\Reply')->create();

        try {
            $this->post("threads/{$reply->thread->slug}/replies/{$reply->id}/favorites");
            $this->post("threads/{$reply->thread->slug}/replies/{$reply->id}/favorites");
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
