<?php

namespace App\Services;


use App\Events\NotificationSent;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Modules\Core\Models\Notification;
use Modules\Core\Models\User;

class NotificationService
{
    /**
     * Create and send notification to specific users
     */
    public function sendToUsers(
        array $userIds,
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        ?string $imageUrl = null,
        ?string $linkUrl = null,
        ?int $senderId = null,
        ?array $data = null
    ): Notification {
        $notification = $this->createNotification(
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            $imageUrl,
            $linkUrl,
            $senderId,
            false,
            $data
        );

        $this->attachUsers($notification, $userIds);
        $this->broadcastNotification($notification, $userIds);

        return $notification;
    }

    /**
     * Create and send global notification
     */
    public function sendGlobal(
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        ?string $imageUrl = null,
        ?string $linkUrl = null,
        ?int $senderId = null,
        ?array $data = null
    ): Notification {
        $notification = $this->createNotification(
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            $imageUrl,
            $linkUrl,
            $senderId,
            true,
            $data
        );

        // For global notifications, we can either:
        // 1. Attach to all existing users
        // 2. Handle dynamically when users request notifications

        // Option 1: Attach to all users (recommended for important notifications)
        $allUserIds = User::pluck('id')->toArray();
        $this->attachUsers($notification, $allUserIds);
        $this->broadcastGlobalNotification($notification);

        return $notification;
    }

    /**
     * Send notification with file link
     */
    public function sendFileNotification(
        array $userIds,
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        string $filePath,
        ?string $imageUrl = null,
        ?int $senderId = null,
        ?array $data = null
    ): Notification {
        return $this->sendToUsers(
            $userIds,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            $imageUrl,
            $filePath,
            $senderId,
            $data
        );
    }

    /**
     * Send notification with URL link
     */
    public function sendUrlNotification(
        array $userIds,
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        string $url,
        ?string $imageUrl = null,
        ?int $senderId = null,
        ?array $data = null
    ): Notification {
        return $this->sendToUsers(
            $userIds,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            $imageUrl,
            $url,
            $senderId,
            $data
        );
    }

    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications(
        User $user,
        int $perPage = 15,
        bool $unreadOnly = false,
        ?string $locale = null
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $locale = $locale ?? app()->getLocale();

        $query = Notification::query()
            ->where(function (Builder $q) use ($user) {
                $q->where('is_global', true)
                    ->orWhereHas('users', function (Builder $subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            })
            ->with(['sender'])
            ->latest();

        if ($unreadOnly) {
            $query->whereDoesntHave('users', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('notification_user.read_at');
            });
        }

        $notifications = $query->paginate($perPage);

        // Transform the data to include localized content
        //        $notifications->getCollection()->transform(function ($notification) use ($locale) {
        //            $notification->localized_title = $notification->getTranslations('title', $locale);
        //            $notification->localized_body = $notification->getTranslations('body', $locale);;
        //            return $notification;
        //        });

        return $notifications;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, User $user): bool
    {
        $notification = Notification::find($notificationId);

        if (!$notification) {
            return false;
        }

        // For global notifications, create pivot record if doesn't exist
        if ($notification->is_global) {
            $notification->users()->syncWithoutDetaching([
                $user->id => ['read_at' => now()]
            ]);
        } else {
            $notification->markAsReadBy($user);
        }

        return true;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        // Update existing pivot records
        $user->notifications()->wherePivot('read_at', null)
            ->updateExistingPivot($user->id, ['read_at' => now()]);

        // Handle global notifications
        $globalNotifications = Notification::where('is_global', true)->get();
        foreach ($globalNotifications as $notification) {
            $notification->users()->syncWithoutDetaching([
                $user->id => ['read_at' => now()]
            ]);
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::query()
            ->where(function (Builder $q) use ($user) {
                // Personal notifications that are unread
                $q->whereHas('users', function (Builder $subQ) use ($user) {
                    $subQ->where('user_id', $user->id)
                        ->whereNull('notification_user.read_at');
                })
                    // Global notifications that user hasn't read
                    ->orWhere(function (Builder $globalQ) use ($user) {
                        $globalQ->where('is_global', true)
                            ->whereDoesntHave('users', function (Builder $readQ) use ($user) {
                                $readQ->where('user_id', $user->id)
                                    ->whereNotNull('notification_user.read_at');
                            });
                    });
            })
            ->count();
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId): bool
    {
        return Notification::destroy($notificationId) > 0;
    }

    /**
     * Create notification record
     */
    private function createNotification(
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        ?string $imageUrl = null,
        ?string $linkUrl = null,
        ?int $senderId = null,
        bool $isGlobal = false,
        ?array $data = null
    ): Notification {
        //        dd([
        //            'title' => [
        //                'en' => $titleEn ?? '',
        //                'ar' => $titleAr ?? '',
        //            ],
        //            'body' => [
        //                'en' => $bodyEn ?? '',
        //                'ar' => $bodyAr ?? '',
        //            ],
        //            'image_url' => $imageUrl,
        //            'link_url' => $linkUrl,
        //            'sender_id' => $senderId,
        //            'is_global' => $isGlobal ?? false,
        //            'data' => $data  ,
        //        ]);
        return Notification::create([
            'title' => [
                'en' => $titleEn ?? '',
                'ar' => $titleAr ?? '',
            ],
            'body' => [
                'en' => $bodyEn ?? '',
                'ar' => $bodyAr ?? '',
            ],
            'image_url' => $imageUrl,
            'link_url' => $linkUrl,
            'sender_id' => $senderId,
            'is_global' => $isGlobal ?? false,
            'data' => $data,
        ]);
    }

    /**
     * Attach users to notification
     */
    private function attachUsers(Notification $notification, array $userIds): void
    {
        $userData = collect($userIds)->mapWithKeys(function ($userId) {
            return [$userId => ['delivered_at' => now()]];
        })->toArray();

        $notification->users()->attach($userData);
    }

    /**
     * Broadcast notification to specific users
     */
    private function broadcastNotification(Notification $notification, array $userIds): void
    {
        foreach ($userIds as $userId) {
            broadcast(new NotificationSent($notification, $userId))->toOthers();
        }
    }

    /**
     * Broadcast global notification
     */
    private function broadcastGlobalNotification(Notification $notification): void
    {

        broadcast(new NotificationSent($notification))->toOthers();
    }

    public static function encodeFilePath(string $filePath): string
    {
        return base64_encode(Crypt::encryptString($filePath));
    }

    public static function decodeFilePath(string $encodedPath): string
    {
        return Crypt::decryptString(base64_decode($encodedPath));
    }
}
