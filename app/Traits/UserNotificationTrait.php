<?php

namespace App\Traits;

use App\Notifications\NotificationApi;
use App\Notifications\user\AccountProfileAcceptedNotification;
use App\Notifications\user\AccountProfileRejectedNotification;
use App\Notifications\user\CompleteAccountProfileNotification;

trait UserNotificationTrait
{


    protected static function notifyCompleteAccountProfile($user)
    {
        // محتوى العنوان متعدد اللغات
        $title = json_encode([
            'ar' => "تم قبول ملفك الشخصي",
            'en' => "Account Accepted"
        ]);

        // محتوى الوصف متعدد اللغات
        $description = json_encode([
            'ar' => "لقد قمت بإكمال بيانات حسابك بنجاح",
            'en' => "You have successfully completed your account profile"
        ]);

        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Profile_Completed'; // نوع الإشعار
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        // الإرسال للمستخدم
        if (!is_null($user->fcm_token)) {
            // لو عايز تفعّل الإرسال عبر Firebase
            // sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }

        // إشعار داخلي عبر Laravel Notifications
        $user->notify($notification);
    }




    protected static function notifyAccountProfileRejected($user, $reason)
    {
        // محتوى العنوان متعدد اللغات
        $title = json_encode([
            'ar' => "تم رفض ملفك الشخصي",
            'en' => "Account Rejected"
        ]);

        // محتوى الوصف متعدد اللغات + السبب
        $description = json_encode([
            'ar' => "عذراً، تم رفض ملفك الشخصي. السبب: " . $reason,
            'en' => "Sorry, your account profile has been rejected. Reason: " . $reason
        ]);

        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Profile Rejected'; // نوع الإشعار

        // تمرير السبب كجزء من بيانات الإشعار
        $notification = new NotificationApi($title, $description, $date, $type, ['reason' => $reason]);

        $title = json_decode($title);
        $lang  = app()->getLocale();

        // الإرسال عبر Firebase (اختياري)
        if (!is_null($user->fcm_token)) {
            // sendFirebaseNotification($title->$lang, $type, $user->fcm_token, [
            //     'reason' => $reason
            // ]);
        }

        // إشعار داخلي عبر Laravel Notifications
        $user->notify($notification);
    }
}
