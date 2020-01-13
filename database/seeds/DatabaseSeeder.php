<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory('App\User')->states('administrator')->create();
        
        factory('App\Category', 5)->create()->each(function($category) {
            factory('App\Thread', 10)
                ->states('with_activity')
                ->create(['category_id' => $category->id])
                ->each(function($thread) {
                    $thread->replies()->saveMany(
                        factory('App\Reply', 10)->states('with_activity')->create(['thread_id' => $thread->id])
                    );
                });
        });
    }
}
