<?php

namespace App\Notifications;

use App\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YouWereMentioned extends Notification
{
    use Queueable;

    /**
     * @var Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param Reply $reply
     */
    public function __construct(Reply $reply)
    {
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
            'message' => $this->reply->author->name . ' mentioned you in ' . $this->reply->thread->title,
            'link' => $this->reply->path()
        ];
    }
}
