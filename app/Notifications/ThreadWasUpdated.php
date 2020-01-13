<?php

namespace App\Notifications;

use App\Reply;
use App\Thread;
use Illuminate\Notifications\Notification;

class ThreadWasUpdated extends Notification
{
    /**
     * The thread that was updated.
     *
     * @var Thread
     */
    protected $thread;

    /**
     * The new reply.
     *
     * @var Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param Thread $thread
     * @param Reply  $reply
     */
    public function __construct(Thread $thread, Reply $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->reply->author->name . ' replied to ' . $this->thread->title,
            'link' => $this->reply->path()
        ];
    }
}
