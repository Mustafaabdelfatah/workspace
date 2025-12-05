<?php
namespace App\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;

class FCMChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message['tokens'] || count($message['tokens']) == 0)
            return;

        foreach ($message['tokens'] as $token) {
            if (!empty($message['data']['url']) && is_array($message['data']['url'])) {
                $message['data']['url'] = $message['data']['url'][$token['agent']] ?? '';
            }
            (new FcmService())->sendNotification($token['token'], $message['title'], $message['body'], $message['data'] ?? []);
        }
    }
}
