<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Mission;
use App\Notifications\NotificationApi;
use Illuminate\Support\Facades\Notification;
use App\Notifications\offer\AcceptMissionNotification;
use App\Notifications\offer\OfferAcceptedNotification;
use App\Notifications\offer\OfferPresenteNotification;
use App\Notifications\offer\OfferCancelledNotification;
use App\Notifications\offer\DeliveredMissionNotification;
use App\Notifications\offer\MissionCancelledNotification;

trait OfferNotificationTrait
{
    protected static function notifyOfferAccepted($user, $mission)
    {
        $descriptionMission = substr($mission->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "تم قبول العرض الخاص بك على مهمة {$descriptionMission}",
            'en' => "Your offer has been accepted for the mission {$descriptionMission}"
        ]);

        $description = json_encode([
            'ar' => "تهانينا! تم قبول العرض الخاص بك على المهمة {$descriptionMission}.",
            'en' => "Congratulations! Your offer has been accepted for the mission {$descriptionMission}."
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Accepted_Offer';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();
        // if (!is_null($user->fcm_token)) {
            sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        // }
        $user->notify($notification);
    // }
}


    protected static function notifyOfferCancelled($mission)
{
    $descriptionMission = substr($mission->description, 0, 50) . ' ...';

    $title = json_encode([
        'ar' => "تم رفض عرضك على مهمة {$descriptionMission}",
        'en' => "Your offer has been rejected for the mission {$descriptionMission}"
    ]);

    $description = json_encode([
        'ar' => "عذراً، تم رفض عرضك على المهمة {$descriptionMission}.",
        'en' => "Your offer has been rejected for the mission {$descriptionMission}."
    ]);

    $date = \Carbon\Carbon::now()->diffForHumans();
    $type = 'Rejected_Offer';

    // جيب كل العروض اللي مش مقبولة
    $offers = $mission->offers()->where('id', '!=', $mission->offerAccepted->id)->get();

    foreach ($offers as $offer) {
        $user = $offer->user;

        $notification = new NotificationApi($title, $description, $date, $type);
        $title = json_decode($title);
        $lang = app()->getLocale();

        if (!is_null($user->fcm_token)) {
            sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }

        $user->notify($notification);
    }
}

}
