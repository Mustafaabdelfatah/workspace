<?php

namespace Modules\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestFcmNotification extends Notification
{
    use Queueable;

    private $title;
    private $body;
    private $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body, $data)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        return [
            'tokens' => $notifiable->routeNotificationForFcm(),
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
