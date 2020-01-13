<?php

namespace Tests\Feature;

use App\Thread;
use App\Trending;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_thread_is_trending()
    {
        $threadOne = factory('App\Thread')->create(['created_at' => Carbon::now()->subMonth()]);
        $threadTwo = factory('App\Thread')->create(['created_at' => Carbon::now()->subDay()]);

        $this->get($threadTwo->path());
        $this->get($threadTwo->path());

        $trending = Thread::trending()->get();

        $this->assertTrue($trending->contains($threadTwo));
        $this->assertFalse($trending->contains($threadOne));
    }
}
