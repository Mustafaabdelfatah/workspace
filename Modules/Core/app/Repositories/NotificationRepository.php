<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Auth;
use DB;
use Modules\Core\Models\Notification;
use Modules\Core\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Modules\Core\Notifications\SysNotification;

class NotificationRepository
{


    public function __construct()
    {
       
    }

    public function getSentNotifications($args)
    {


        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 15;


        $query = Notification::whereNotNull('sender_id')
            ->with(['sender'])
            ->latest();


        $notifications = $query->paginate($perPage);


        return [
            'status' => !$notifications->isEmpty(),
            'message' => (($notifications->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => [
                'total' => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ],
            'records' => $notifications,
        ];
    }


    public function sendNotification($args)
    {
        $args['sender_id'] = auth()->id();

        if(!empty($args['image'])){
            $args['image_url'] = Storage::disk('local')->putFile('uploads/notifications/', $args['image']);
        }

        if(!empty($args['file'])){
            $args['link_url'] = Storage::disk('local')->putFile('uploads/notifications/', $args['file']);
        }

        if (empty($args['user_ids'])) {
            $args['is_global'] = true;
        } else {
            $args['is_global'] = false;
        }
        $notification = Notification::create($args);

        if (!empty($args['user_ids'])) {
            $userData = collect($args['user_ids'])->mapWithKeys(function ($userId) {
                return [$userId => ['delivered_at' => now()]];
            })->toArray();
            FacadesNotification::send(User::whereIn('id', $args['user_ids'])->get(), new SysNotification($notification));
        }
        else{
            FacadesNotification::send(User::all(), new SysNotification($notification));
        }

     
        return ['status' => true, 'message' => __('lang_notification_sent')];
    }

    public function getNotifications($args)
    {


        $user = Auth::user();
        $unreadOnly = $args['unread_only'] ?? false;
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 15;

        $query = Notification::where('created_at', '>=', auth()->user()->created_at)
            ->where(function ($q) use ($user) {
                $q->where('is_global', true)
                    ->orWhereHas('users', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            })
            ->with(['sender'])
            ->latest();

        if ($unreadOnly) {
            $query->whereDoesntHave('users', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('notification_user.read_at');
            });
        }


        $notifications = $query->paginate($perPage);


        return [
            'status' => !$notifications->isEmpty(),
            'message' => (($notifications->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => [
                'total' => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ],
            'records' => $notifications,
        ];
    }

    public function unreadNotificationsCount($args)
    {
        $user = auth()->user();

       
        $count =   Notification::where('created_at', '>=', auth()->user()->created_at)->when($user->last_notifications_click, function ($query) use ($user) {
            $query->where('created_at', '>',$user->last_notifications_click);
        })
            ->where(function ($q) use ($user) {
                $q->whereHas('users', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id)
                        ->whereNull('notification_user.read_at');
                })
                    ->orWhere(function ($globalQ) use ($user) {
                        $globalQ->where('is_global', true)
                            ->whereDoesntHave('users', function ($readQ) use ($user) {
                                $readQ->where('user_id', $user->id)
                                    ->whereNotNull('notification_user.read_at');
                            });
                    });
            })
            ->count();

        return  $count;
    }

    public function markNotificationAsRead($args)
    {
        /** @var User $user */
        $user = auth()->user();

        if (!empty($args['notification_id'])) {
            $notification = Notification::find($args['notification_id']);

            if ($notification->is_global) {
                $notification->users()->syncWithoutDetaching([
                    $user->id => ['read_at' => now()]
                ]);
            } else {
                $notification->markAsReadBy($user);
            }
        } else {
            $user->notifications()->wherePivot('read_at', null)
                ->updateExistingPivot($user->id, ['read_at' => now()]);


            $globalNotifications = Notification::where('is_global', true)->get();
            foreach ($globalNotifications as $notification) {
                $notification->users()->syncWithoutDetaching([
                    $user->id => ['read_at' => now()]
                ]);
            }
        }

        return [
            'status' => true,
            'message' => __('lang_marked_as_read')
        ];
    }

    public function deleteNotification($args)
    {
        $notificationId = $args['notification_id'];
        $notification = Notification::find($notificationId);
        $notification->delete();
        return ['status' => true, 'message' => __('langnotification_deleted')];
    }


    public function updateLastNotificationsClick($args)
    {
        $user = auth()->user();
        $user->update(['last_notifications_click' => now()]);

        return ['status' => true, 'message' => __('lang_last_notifications_click_updated')];
    }
}
