<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    public function test_a_thread_has_a_path()
    {
        $this->assertEquals("/threads/{$this->thread->slug}", $this->thread->path());
    }

    public function test_a_thread_has_an_author()
    {
        $this->assertInstanceOf('App\User', $this->thread->author);
    }

    public function test_a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    public function test_a_thread_belongs_to_a_category()
    {
        $this->assertInstanceOf('App\Category', $this->thread->category);
    }

    public function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply(['content' => 'Foobar','user_id' => 1]);

        $this->assertCount(1, $this->thread->replies);
    }

    public function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->be(factory('App\User')->create());

        $this->thread->subscribe();
        $this->thread->addReply(['content' => 'Foobar', 'user_id' => 999]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    public function test_a_thread_can_be_subscribed_to()
    {
        $this->thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    public function test_a_thread_can_be_unsubscribed_from()
    {
        $this->thread->subscribe($userId = 1);

        $this->thread->unsubscribe($userId);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    public function test_it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $this->be(factory('App\User')->create());

        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    public function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create();

        $this->assertTrue($thread->hasUpdatesFor($user));

        $user->read($thread);

        $this->assertFalse($thread->hasUpdatesFor($user));
    }

    public function test_thread_content_is_sanitized_automatically()
    {
        $thread = factory('App\Thread')
            ->make(['content' => '<script>alert("bad")</script><p>This is okay.</p>']);
        
        $this->assertEquals("<p>This is okay.</p>", $thread->content);
    }
}
