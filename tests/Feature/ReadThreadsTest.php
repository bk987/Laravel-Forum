<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
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

    public function test_a_user_can_view_all_threads()
    {
        $this->get(route('threads.index'))
            ->assertSee($this->thread->title);
    }

    public function test_a_user_can_view_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    public function test_a_user_can_filter_threads_by_categories()
    {
        $category = factory('App\Category')->create();
        $threadInCategory = factory('App\Thread')->create(['category_id' => $category->id]);
        $threadNotInCategory = factory('App\Thread')->create();

        $this->get('/categories/' . $category->slug)
            ->assertSee($threadInCategory->title)
            ->assertDontSee($threadNotInCategory->title);
    }

    public function test_a_user_can_filter_threads_by_author()
    {
        $this->be($user = factory('App\User')->create(['name' => 'John']));
        $threadByJohn = factory('App\Thread')->create(['user_id' => $user->id]);
        $threadNotByJohn = factory('App\Thread')->create();

        $this->get('/threads?author=' . $user->name)
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    public function test_a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = factory('App\Thread')->create();
        factory('App\Reply', 3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    public function test_a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = factory('App\Thread')->create();
        factory('App\Reply')->create(['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    public function test_a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    public function test_it_records_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = factory('App\Thread')->create();

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
