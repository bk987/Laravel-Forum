<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->be(factory('App\User')->create());
    }

    public function test_a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply()
    {
        $thread = factory('App\Thread')->create()->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'content' => 'Some reply here'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => factory('App\User')->create()->id,
            'content' => 'Some reply here'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    public function test_a_user_can_fetch_their_unread_notifications()
    {
        factory('Illuminate\Notifications\DatabaseNotification')->create();

        $this->assertCount(1, $this->getJson('/profiles/' . auth()->user()->name . '/notifications')->json());
    }

    public function test_a_user_can_mark_a_notification_as_read()
    {
        factory('Illuminate\Notifications\DatabaseNotification')->create();

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);

            $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);

            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}
