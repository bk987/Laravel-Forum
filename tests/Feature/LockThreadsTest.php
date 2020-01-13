<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_administrators_cannot_lock_threads()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }
    
    public function test_administrators_can_lock_threads()
    {
        $this->be($user = factory('App\User')->states('administrator')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }
    
    public function test_administrators_can_unlock_threads()
    {
        $this->be($user = factory('App\User')->states('administrator')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id, 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }
    
    public function test_a_locked_thread_cannot_receive_new_replies()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['locked' => true]);

        $this->post($thread->path() . '/replies', [
                'content' => 'Foobar',
                'user_id' => $user->id
            ])
            ->assertStatus(422);
    }
}
