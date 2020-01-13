<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Thread;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory('App\User')->create()->id;
        },
        'category_id' => function() {
            return factory('App\Category')->create()->id;
        },
        'title' => $faker->sentence,
        'content' => $faker->paragraph,
        'visits' => 0,
        'locked' => false
    ];
});

$factory->state(Thread::class, 'with_activity', []);
$factory->afterCreatingState(Thread::class, 'with_activity', function ($thread, $faker) {
    $thread->activity()->create([
        'user_id' => $thread->user_id,
        'type' => 'created_thread'
    ]);
});