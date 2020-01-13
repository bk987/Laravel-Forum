<?php

namespace Tests\Unit;

use App\Reply;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_reply_has_an_author()
    {
        $reply = factory('App\Reply')->create();

        $this->assertInstanceOf('App\User', $reply->author);
    }

    public function test_it_knows_if_it_was_just_published()
    {
        $reply = factory('App\Reply')->create();

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    public function test_it_can_detect_all_mentioned_users_in_the_content()
    {
        $reply = factory('App\Reply')->create(['content' => '@JaneDoe wants to talk to @JohnDoe']);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }

    public function test_it_wraps_mentioned_usernames_in_the_content_within_anchor_tags()
    {
        $reply = factory('App\Reply')->create(['content' => 'Hello @JaneDoe.']);

        $this->assertEquals('Hello <a href="/profiles/JaneDoe">@JaneDoe</a>.', $reply->content);
    }

    public function test_it_knows_if_it_is_the_best_reply()
    {
        $reply = factory('App\Reply')->create();

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    public function test_reply_content_is_sanitized_automatically()
    {
        $reply = factory('App\Reply')
            ->make(['content' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals('<p>This is okay.</p>', $reply->content);
    }
}
