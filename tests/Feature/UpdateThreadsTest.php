<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_unauthorized_user_cannot_update_threads()
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->create();

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    public function test_a_thread_requires_a_title_and_content_to_be_updated()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->patch($thread->path(), ['title' => 'Changed'])
            ->assertSessionHasErrors('content');

        $this->patch($thread->path(), ['content' => 'Changed'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_thread_can_only_be_updated_by_its_author()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->patch($thread->path(), [
            'category_id' => $thread->category->id,
            'title' => 'Changed',
            'content' => 'Changed content.'
        ]);

        tap($thread->fresh(), function ($thread) {
            $this->assertEquals('Changed', $thread->title);
            $this->assertEquals('Changed content.', $thread->content);
        });
    }

    protected function postThread($overrides = [])
    {
        $this->be(factory('App\User')->create());
        $thread = factory('App\Thread')->make($overrides);

        return $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
