<?php

namespace App\Traits;

use App\Models\Mission;
use App\Notifications\NotificationApi;
use App\Notifications\Mission\DeliveredMissionNotification;
use App\Notifications\Mission\MissionCancelledNotification;
use App\Models\User;

trait MissionNotificationTrait
{

    // MissionDisputed



    protected static function notifyMissionDisputed(Mission $mission)
    {
        $descriptionMission = substr($mission->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "تم تقديم طلب التحكيم {$descriptionMission}",
            'en' => "Your mission has been disputed {$descriptionMission}"
        ]);

        $description = json_encode([
            'ar' => "عذراً، تم تقديم طلب التحكيم {$descriptionMission}.",
            'en' => "Your mission has been disputed {$descriptionMission}."
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Disputed_Mission';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $mission->user;

        if (!is_null($user->fcm_token)) {//do loop
            sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }
        $user->notify($notification);
    }

    protected static function notifyDeliveredMission(Mission $mission)
    {
        $descriptionMission = substr($mission->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "تم توصيل المهمة بنجاح {$descriptionMission}",
            'en' => "Your mission has been delivered successfully {$descriptionMission}"
        ]);

        $description = json_encode([
            'ar' => "مبروك تم توصيل المهمة الخاصة بك {$descriptionMission}.",
            'en' => "Your mission has been delivered successfully {$descriptionMission}."
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Delivered Mission';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $mission->user;

        if (!is_null($user->fcm_token)) {
            sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }
        $user->notify($notification);
    }

    protected static function notifyMissionCancelled(Mission $mission)
    {
        $descriptionMission = substr($mission->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "تم إلغاء المهمة الخاصة بك {$descriptionMission}.",
            'en' => "Your mission has been cancelled {$descriptionMission}."
        ]);

        $description = json_encode([
            'ar' => "عذراً، تم إلغاء المهمة الخاصة بك {$descriptionMission}.",
            'en' => "Your mission has been cancelled {$descriptionMission}."
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Cancelled_Mission';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $mission->user;

        if (!is_null($user->fcm_token)) {
            sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }
        $user->notify($notification);
    }


    protected static function notifyMissionResolved(Mission $mission, $users)
    {
        $descriptionMission = substr($mission->description, 0, 50) . ' ...';

        $title = json_encode([
            'ar' => "تم حل النزاع في المهمة: {$descriptionMission}.",
            'en' => "The dispute for your mission has been resolved: {$descriptionMission}."
        ]);

        $description = json_encode([
            'ar' => "تم إصدار الحكم وتم حل النزاع. الفائز بالمهمة هو المستخدم رقم.",
            'en' => "The dispute has been resolved. The winner of the mission is user ID."
        ]);

        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Resolved Mission';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        // $winner = User::find($userId);

        if ($users && !is_null($users['user_offer']->fcm_token) || $users && !is_null($users['user_mission']->fcm_token)) {
            sendFirebaseNotification($title->$lang, $type, $users['user_offer']->fcm_token);
        }

        if ($users) {
            $users['user_offer']->notify($notification);
            $users['user_mission']->notify($notification);

        }
    }
}
