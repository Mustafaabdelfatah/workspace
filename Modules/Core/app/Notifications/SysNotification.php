<?php

namespace Modules\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SysNotification extends Notification implements ShouldQueue
{

    use Queueable;
    private $notification;

    /**
     * Create a new notification instance.
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['broadcast','fcm'];
    }


    public function toFcm($notifiable)
    {

        $locale = request()->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'ar') ? 'ar' : 'en';
        return [
            'tokens' => $notifiable->routeNotificationForFcm(),
            'title' => $this->notification->getTranslation('title', $locale),
            'body' => $this->notification->getTranslation('body', $locale),
            'data' => [
            'id' => (string)$this->notification->id,
            'image_url' => $this->notification->image_url ?? null, 
            'link_url' => $this->notification->link_url ?? null,
            'action_type' => $this->notification->getActionType(),
            'sender' => $this->notification->sender ? json_encode([
                'id' => $this->notification->sender->id,
                'name' => $this->notification->sender->name,
            ]) : null,
            'is_read' => "0",
            'data' => json_encode($this->notification->data),
            'created_at' => now()->toISOString(),
        ]
        ];
    }

    public function toBroadcast($notifiable)
    {
        $locale = request()->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'ar') ? 'ar' : 'en';

        return new BroadcastMessage([
            'id' => $this->notification->id,
            'title' => $this->notification->getTranslation('title', $locale),
            'body' => $this->notification->getTranslation('body', $locale),
            'image_url' => $this->notification->image_url,
            'link_url' => $this->notification->link_url,
            'action_type' => $this->notification->getActionType(),
            'sender' => $this->notification->sender ? [
                'id' => $this->notification->sender->id,
                'name' => $this->notification->sender->name,
            ] : null,
            'data' => $this->notification->data,
            'created_at' => now()->toISOString(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'notification.sent';
    }
}
