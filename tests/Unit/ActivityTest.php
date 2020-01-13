<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_records_activity_when_a_thread_is_created()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => $user->id,
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = Activity::first();
        $this->assertEquals($activity->subject->id, $thread->id);
    }
    
    public function test_it_records_activity_when_a_reply_is_created()
    {
        $this->be(factory('App\User')->create());
        factory('App\Reply')->create();

        $this->assertEquals(2, Activity::count());
    }
    
    public function test_it_fetches_a_feed_for_any_user()
    {
        $this->be($user = factory('App\User')->create());
        factory('App\Thread', 2)->create(['user_id' => $user->id]);

        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user(), 50);
        
        $this->assertTrue($feed->keys()->contains(Carbon::now()->format('Y-m-d')));
        
        $this->assertTrue($feed->keys()->contains(Carbon::now()->subWeek()->format('Y-m-d')));
    }
}
