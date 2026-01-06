<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Setting;
use App\Repositories\Dashboard\Contracts\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function markAsRead($id)
    {
        $notification = auth('admin')->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->update(['is_read' => true]);
        }
        return $notification;
    }

    public function markAllAsRead()
    {
        // نحدّث كل الإشعارات غير المقروءة
        auth('admin')->user()->unreadNotifications()->update(['is_read' => true]);
        return true;
    }

    public function loadMore($type, $next)
    {
        if ($type == 'unread-load-more')
            // $notifications = Admin::first()->unreadNotifications();
            $notifications = auth('admin')->user()->unreadNotifications();
        else
            $notifications = auth('admin')->user()->notifications();

        $notifications = $notifications->skip($next)->take(10)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'color' => $notification->data['color'],
                'icon' => $notification->data['icon'],
                'title_ar' => $notification->data['title_ar'],
                'title_en' => $notification->data['title_en'],
                'description_ar' => $notification->data['description_ar'],
                'description_en' => $notification->data['description_en'],
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });


        return $notifications;
    }

    public function saveToken($request)
    {
        return auth('admin')->user()->update(['device_token' => $request->token]);
    }

    public function changeSoundStatus($request)
    {
        Setting::setExtraColumns([
            'user_id' => auth('admin')->id()
        ]);

        $status = $request->status == "true";
        setting(['notification_status' => $status])->save();

        if ($status) {
            return "تم تفعيل صوت الاشعارات بنجاح";
        } else {
            return "تم إيقاف صوت الاشعارات بنجاح";
        }
    }
}
