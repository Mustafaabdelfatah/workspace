<?php
namespace App\Events;

 use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 use Modules\Core\Models\Notification;

 class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;
    public ?int $userId;

    public function __construct(Notification $notification, ?int $userId = null)
    {
        $this->notification = $notification->load('sender');
        $this->userId = $userId;

    }

    public function broadcastOn(): array
    {
       // if ($this->notification->is_global) {
            return [new Channel('global-notifications')];
       // }

      //  return [new PrivateChannel('user.' . $this->userId)];
    }

    public function broadcastAs(): string
    {
        return 'notification.sent';
    }

    public function broadcastWith(): array
    {
        $locale = request()->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'ar') ? 'ar' : 'en';

        return [
            'id' => $this->notification->id,
            'title' => $this->notification->getTranslation('title', $locale),
            'body' => $this->notification->getTranslation('body', $locale),
            'image_url' => $this->notification->image_url,
            'link_url' => $this->notification->getProcessedLink(),
            'action_type' => $this->notification->getActionType(),
            'sender' => $this->notification->sender ? [
                'id' => $this->notification->sender->id,
                'name' => $this->notification->sender->name,
            ] : null,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(),
        ];
    }
}
