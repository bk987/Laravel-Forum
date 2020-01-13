<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_category_consists_of_threads()
    {
        $category = factory('App\Category')->create();
        $thread = factory('App\Thread')->create(['category_id' => $category->id]);

        $this->assertTrue($category->threads->contains($thread));
    }
}
