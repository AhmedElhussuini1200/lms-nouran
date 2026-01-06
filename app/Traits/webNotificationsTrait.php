<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Admin;
use App\Models\Status;
use App\Models\Mission;
use App\Models\Abstracte;
use App\Models\MissionLog;
use App\Models\ExtinguisherLog;
use Illuminate\Support\Manager;
use App\Notifications\NewNotification;

trait webNotificationsTrait
{
    protected static function newMessageNotification($message)
    {
        $sender = auth()->user();
        $chat   = $message->chat; // نفترض أن عندك علاقة chat في message
        $participants = $chat->users()->where('id', '!=', $sender->id)->get(); // كل المشتركين عدا المرسل

        $titleAr = "رسالة جديدة من " . $sender->name;
        $titleEn = "New message from " . $sender->name;
        $descriptionAr = $message->body;
        $descriptionEn = $message->body; // لو فيه ترجمة استخدمها
        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = "primary";

        foreach ($participants as $user) {
            // تحديد الرابط حسب نوع المستخدم
            if ($user->type == 'admin' || $user->type == 'amana') {
                $showRoute = route("dashboard.admin.chats.show", $chat->id);
            } elseif ($user->type == 'consultant') {
                $showRoute = route("dashboard.consultant.chats.show", $chat->id);
            } elseif ($user->type == 'contractor') {
                $showRoute = route("dashboard.contractor.chats.show", $chat->id);
            }

            storeAndPushNotificationUser(
                $titleAr,
                $titleEn,
                $descriptionAr,
                $descriptionEn,
                $icon,
                $color,
                $showRoute,
                [$user->id]
            );
        }
    }

    // first notification on contractor dashboard to manager
    protected static function newContractitemNotification($contractItem)
    {
        $contractor = auth()->user();
        $creatorId = $contractItem->created_by ?? $contractItem->contractor_id ?? null;
        $typeUser = $contractItem->createdBy->type;

        $titleAr       = "طلب تقديم بنود من " . $contractor->name;
        $titleEn       = "Request Arbitration";
        $descriptionAr = "تم تقديم طلب الرجاء مراجعته.";
        $descriptionEn = "Arbitration request has been submitted, please review it.";
        $icon          = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                            </svg>';
        $color         = "success";



        $manager = Admin::whereHas('roles', function ($q) {
            $q->where('name_ar', 'مدير تنفيذي'); // الاسم باللي موجود عندك في جدول roles
        })->first();

        if ($manager) {
            $recipients = $manager->id;

            if ($manager->type == 'admin' || $manager->type == 'amana') {
                $showRoute = route("dashboard.admin.contractItems.show", $contractItem->id);
            } elseif ($manager->type == 'consultant') {
                $showRoute = route("dashboard.consultant.contractItems.show", $contractItem->id);
            } elseif ($manager->type == 'contractor') {
                $showRoute = route("dashboard.contractor.contractItems.show", $contractItem->id);
            }
        } else {
            // مفيش مدير → ابعت لنفس صاحب البند
            $recipients = $contractItem->created_by ?? $contractItem->user_id;

            // والمسار حسب دور الشخص اللي أنشأ البند
            $creator = Admin::find($recipients);
            if ($creator->type == 'consultant') {
                $showRoute = route("dashboard.consultant.contractItems.show", $contractItem->id);
            } elseif ($creator->type == 'contractor') {
                $showRoute = route("dashboard.contractor.contractItems.show", $contractItem->id);
            }
        }



        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }

    protected static function sendMissionStatusNotification(Mission $mission, $status)
    {
        $creatorId = $mission->user_id ?? null; // أو $mission->created_by لو انت مخزن الادمن اللي انشأ المهمة
        if (!$creatorId) {
            return;
        }

        $titleAr = $status === 'approved' ? 'تم اعتماد المهمة' : 'تم رفض المهمة';
        $titleEn = $status === 'approved' ? 'Mission Approved' : 'Mission Rejected';

        $descriptionAr = $status === 'approved'
            ? 'تمت الموافقة على مهمتك من قبل الإدارة.'
            : 'تم رفض مهمتك، يرجى مراجعة السبب.';
        $descriptionEn = $status === 'approved'
            ? 'Your mission has been approved by the admin.'
            : 'Your mission has been rejected, please review the reason.';

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $status === 'approved' ? 'success' : 'danger';
        $showRoute = route('dashboard.missions.show', $mission->id);

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            [$creatorId]
        );
    }

    protected static function sendMissionAssignedNotification($missionLog, int $step = 1, int $total = 1)
    {
        $sender = auth()->user();
        $recipientId = $missionLog->to_user_id;

        $titleAr = "تم تعيين مهمة لك من " . $sender->name;
        $titleEn = "You have been assigned a mission by " . $sender->name;

        // نضمّن رقم الخطوة/التقدم داخل الوصف (يمكن تغييره حسب رغبتك)
        $descriptionAr = "تم تعيين مهمة جديدة لك. ({$step}/{$total}) — الرجاء مراجعة المهمة.";
        $descriptionEn = "A new mission has been assigned to you. ({$step}/{$total}) — Please review it.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = 'primary';

        // تحديد رابط العرض حسب نوع المستخدم (راجع الحقول في جدولك)
        $user = \App\Models\Admin::find($recipientId);
        if (!$user) {
            return false;
        }

        if ($user->type == 'admin' || $user->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $missionLog->mission_id);
        } elseif ($user->type == 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $missionLog->mission_id);
        } else {
            $showRoute = route("dashboard.contractor.missions.show", $missionLog->mission_id);
        }

        // استدعاء الهيلبر الموجود عندك
        // storeAndPushNotificationUser يعيد نتيجة طلب FCM أو null
        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );

        // لو حابب تحلل قيمة $response لترجع true/false:
        // if ($response instanceof \Illuminate\Http\Client\Response) {
        //     // لو رجع 200 أو 201 اعتبره ناجح
        //     return $response->successful();
        // }

        // // لو null، اعتبرنا اننا أرسلنا عبر Notification facade (نجاح ضمني)
        // return true;
    }

    protected static function newApprovedContractitemNotification($contractItem)
    {



        $titleAr = "تم اعتماد البند";
        $titleEn = "Contract Item Approved";
        $descriptionAr = "تمت الموافقة على البند الخاص بك من قبل الأمانة.";
        $descriptionEn = "Your contract item has been approved by the admin.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';

        $color = "success";

        $contractor = auth()->user();
        $creatorId = $contractItem->created_by ?? $contractItem->contractor_id ?? null;
        $typeUser = $contractItem->createdBy->type;




        if ($typeUser == "admin" || $typeUser == 'amana') {
            $showRoute = route("dashboard.admin.contractItems.show", $contractItem->id);
        } elseif ($typeUser == "contractor") {
            $showRoute = route("dashboard.contractor.contractItems.show", $contractItem->id);
        } elseif ($typeUser == "consultant") {
            $showRoute = route("dashboard.consultant.contractItems.show", $contractItem->id);
        }
        if ($creatorId) {
            storeAndPushNotificationUser(
                $titleAr,
                $titleEn,
                $descriptionAr,
                $descriptionEn,
                $icon,
                $color,
                $showRoute,
                [$creatorId]
            );
        }
    }


    protected static function newAbstractNotification($abstracte)
    {
        $user = auth()->user();

        $abstracte->load('createdBy');
        $typeUser = $user->type;


        // الشخص الذي أنشأ الإطفاء نفسه
        $abstracteCreator = $abstracte->createdBy;

        // تحديد الشخص المستلم للإشعار
        // $recipientId = $manager ?? $creator->id;

        $titleAr       = "طلب تقديم مستخلص " . $abstracte->name;
        $titleEn       = "Request Arbitration " . $typeUser;
        $descriptionAr = "تم تقديم طلب الرجاء مراجعته.";
        $descriptionEn = "Arbitration request has been submitted, please review it.";
        $icon          = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                            </svg>';
        $color         = "success";


        $manager = Admin::whereHas('roles', function ($q) {
            $q->where('name_ar', 'مدير تنفيذي'); // الاسم باللي موجود عندك في جدول roles
        })->first();

        if ($manager) {
            $recipients = $manager->id;

            if ($manager->type == 'admin' || $manager->type == 'amana') {
                $showRoute = route("dashboard.admin.abstractes.show", $abstracte->id);
            } elseif ($manager->type == 'consultant') {
                $showRoute = route("dashboard.consultant.abstractes.show", $abstracte->id);
            } elseif ($manager->type == 'contractor') {
                $showRoute = route("dashboard.contractor.abstractes.show", $abstracte->id);
            }
        }

        // $showRoute = route("dashboard.admin.abstractes.index", $abstracte->id);




        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }



    // protected static function newAbstractNotification($abstracte)
    // {
    //     // حمّل علاقة createdBy
    //     $abstracte->load('createdBy');

    //     // هنا createdBy هو Model وليس Collection
    //     $creator = $abstracte->createdBy;

    //     // نوع المستخدم الذي أنشأ المستخلص
    //     $typeUser = $creator->type;

    //     // تجهيز النصوص
    //     $titleAr       = "طلب تقديم مستخلص " . $abstracte->name;
    //     $titleEn       = "Request Arbitration " . $typeUser;
    //     $descriptionAr = "تم تقديم طلب الرجاء مراجعته.";
    //     $descriptionEn = "Arbitration request has been submitted, please review it.";
    //     $icon          = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
    //                         <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
    //                         <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
    //                         </svg>';
    //     $color         = "success";

    //     // من هو المستلم؟
    //     $contractor = auth()->user();
    //     $manager    = $contractor->manager;
    //     // لا يوجد مدير → الإشعار يذهب للمنشئ نفسه

    //     // لديه مدير


    //     $recipients = $manager->id;

    //     if ($manager->type == 'admin' || $manager->type == 'amana') {
    //         $showRoute = route("dashboard.admin.abstractes.show", $abstracte->id);
    //     }


    //     // إرسال الإشعار
    //     storeAndPushNotificationUser(
    //         $titleAr,
    //         $titleEn,
    //         $descriptionAr,
    //         $descriptionEn,
    //         $icon,
    //         $color,
    //         $showRoute,
    //         $recipients,
    //     );
    // }


    protected static function newApprovedAbstractNotification($abstracte)
    {


        $titleAr = "تم اعتماد المستخلص";
        $titleEn = "Abstracte Approved";
        $descriptionAr = "تمت الموافقة على المستخلص  الخاص بك من قبل الأمانة.";
        $descriptionEn = "Your  abstracte has been approved by the admin.";
        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';

        $color = "success";

        $contractor = auth()->user();
        $creatorId = $abstracte->created_by ?? $abstracte->contractor_id ?? null;
        $typeUser = $abstracte->createdBy->type;




        if ($typeUser == "contractor") {
            $showRoute = route("dashboard.contractor.abstractes.show", $abstracte->id);
        } elseif ($typeUser == "consultant") {
            $showRoute = route("dashboard.consultant.abstractes.show", $abstracte->id);
        }
        if ($creatorId) {
            storeAndPushNotificationUser(
                $titleAr,
                $titleEn,
                $descriptionAr,
                $descriptionEn,
                $icon,
                $color,
                $showRoute,
                [$creatorId]
            );
        }
    }


    protected static function newRecordMissionNotification($mission)
    {
        $creator = auth()->user();
        $creatorForMission = $creator->name;

        // ✔️ جلب بيانات الأولوية
        $priorityName = $mission->priority->name ?? 'بدون أولوية';
        $priorityColor = $mission->priority->color ?? '#4CAF50'; // لون افتراضي

        // ✔️ تعديل العنوان ليظهر الأولوية
        $titleAr = "تم تسجيل مهمة جديدة ($priorityName) بواسطة $creatorForMission";
        $titleEn = "New Mission Recorded ($priorityName)";

        // ✔️ الوصف يظهر فيه نوع الأولوية
        $descriptionAr = "تم تقديم مهمة جديدة حالتها: $priorityName. الرجاء مراجعتها.";
        $descriptionEn = "A new mission has been added with priority: $priorityName. Please review it.";

        // ✔️ استخدام اللون الخاص بالأولوية
        $color = $priorityColor;

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';


        $manager = $creator->manager;

        if ($manager) {
            $recipients = $manager->id;

            $showRoute = route("dashboard.admin.missions.show", $mission->id);

            storeAndPushNotificationUser(
                $titleAr,
                $titleEn,
                $descriptionAr,
                $descriptionEn,
                $icon,
                $color,
                $showRoute,
                $recipients
            );
        }
    }

    protected static function contractorCompletedMissionNotification($log)
    {
        $fromUser = $log->fromUser; // المقاول
        $assignee = $log->toUser;   // المستشار
        $mission = $log->mission;

        if (!$assignee) {
            return;
        }

        $titleAr = "تم تنفيذ المهمة من قبل المقاول " . $fromUser->name;
        $titleEn = "The mission was executed by contractor " . $fromUser->name;

        $descriptionAr = "قام المقاول " . $fromUser->name . " بتنفيذ هذه المهمة. يرجى مراجعة التفاصيل.";
        $descriptionEn = "Contractor " . $fromUser->name . " executed this mission. Please review the details.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = "info";

        $recipients = [$assignee->id];

        if ($assignee->type == 'admin' || $assignee->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        } elseif ($assignee->type == 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $mission->id);
        } elseif ($assignee->type == 'contractor') {
            $showRoute = route("dashboard.contractor.missions.show", $mission->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }

    protected static function newMissionAssignToContractorForReviewNotification($log)
    {
        $fromUser = $log->fromUser;
        $assignee = $log->toUser; // المستشار
        $mission = $log->mission;

        if (!$assignee) {
            return;
        }

        // عناوين الرسالة
        $titleAr = "تم تعيينك لمراجعة المهمة بعد التنفيذ";
        $titleEn = "You have been assigned to review the completed task";

        // نص الرسالة
        $descriptionAr = $fromUser->name . " قام المقاول بتنفيذ المهمة، يرجى مراجعة المهمة والموافقة عليها.";
        $descriptionEn = $fromUser->name . " completed the task. Please review and approve it.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = "success";

        // تحديد الرابط حسب نوع المستخدم
        if ($assignee->type == 'admin' || $assignee->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        } elseif ($assignee->type == 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $mission->id);
        } elseif ($assignee->type == 'contractor') {
            $showRoute = route("dashboard.contractor.missions.show", $mission->id);
        } else {
            $showRoute = "#";
        }

        // إرسال الإشعار
        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            [$assignee->id]
        );
    }

    protected static function newMissionAssignToConsultantNotification($log)
    {
        $fromUser = $log->fromUser;
        $assignee = $log->toUser;
        $missioncreate = $log->mission;

        $titleAr = " تم تعيينك  للمهمة من قبل " . $fromUser->name;
        $titleEn = "The task was reviewed and  was appointed for the task " . $fromUser->name;

        $descriptionAr = $fromUser->name . " قام المدير  بتعيينك " . $assignee->name . " لهذه المهمة، يرجى تنفيذ المطلوب ومراجعة تفاصيل المهمة.";
        $descriptionEn = $fromUser->name . " assigned " . $assignee->name . " to this mission. Please review and execute the task.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = "success";

        if (!$assignee) {
            return;
        }

        // تحويل الرقم إلى مصفوفة
        $recipients = [$assignee->id];

        if ($assignee->type == 'admin' || $assignee->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $missioncreate->id);
        } elseif ($assignee->type == 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $missioncreate->id);
        } elseif ($assignee->type == 'contractor') {
            $showRoute = route("dashboard.contractor.missions.show", $missioncreate->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }
    protected static function sendMissionCompletedNotification($log, $participant)
    {
        $fromUser = $log->fromUser;
        $mission = $log->mission;

        // العنوان والوصف بناءً على اللغة
        $titleAr = "تم إتمام المهمة " . $mission->title . " من قبل " . $fromUser->name;
        $titleEn = "The task " . $mission->title . " has been completed by " . $fromUser->name;

        $descriptionAr = $fromUser->name . " أكمل المهمة " . $mission->title . "، يرجى التحقق من التفاصيل.";
        $descriptionEn = $fromUser->name . " has completed the task " . $mission->title . ". Please check the details.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = "info"; // تحديد اللون للإشعار

        // إذا كان هناك طرف مشارك
        if (!$participant) {
            return;
        }

        // تحديد الرابط الذي سيأخذ المستخدم إلى تفاصيل المهمة
        if ($participant->type == 'admin' || $participant->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        } elseif ($participant->type == 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $mission->id);
        } elseif ($participant->type == 'contractor') {
            $showRoute = route("dashboard.contractor.missions.show", $mission->id);
        }

        // إرسال الإشعار
        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            [$participant->id] // إرسال الإشعار إلى هذا الشخص فقط
        );
    }

    protected static function newApprovedMissionNotification($mission)
    {
        $titleAr = "تم قبول المهمة";
        $titleEn = "The mission has been accepted";
        $descriptionAr = "تمت الموافقة على المهمة الخاصة بك من قبل المدير.";
        $descriptionEn = "Your mission has been approved by the manager";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                        </svg>';

        $color = "success";

        // المستخدم الذي قام بعمل المهمة
        $creator = $mission->createdBy; // تأكد أن لديك علاقة createdBy في الموديل
        $recipients = $creator->id ?? null;

        // تحديد الرابط حسب نوع المستخدم الذي أنشأ المهمة
        if ($creator->type == 'admin' || $creator->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }


    protected static function newRejectedMissionNotification($mission)
    {
        $titleAr = "تم رفض المهمة";
        $titleEn = "The mission has been rejected";
        $descriptionAr = "تم رفض المهمة الخاصة بك من قبل المدير.";
        $descriptionEn = "Your mission has been rejected by the manager";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                        </svg>';

        $color = "danger"; // عادةً نستخدم danger للرفض

        // المستخدم الذي قام بعمل المهمة
        $creator = $mission->createdBy; // تأكد أن لديك علاقة createdBy في الموديل
        $recipients = $creator->id ?? null;

        // تحديد الرابط حسب نوع المستخدم اللي أنشأ المهمة
        if ($creator->type == 'admin' || $creator->type == 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }
    protected function newApprovedContractorMissionNotification($mission)
    {
        // آخر log فيه status = 12 وفيه result
        $lastLog = $mission->logs()
            ->where('status_id', 12)
            ->whereNotNull('result')
            ->latest('id')
            ->first();

        // حماية من null
        if (!$lastLog || !$lastLog->fromUser) {
            return false;
        }

        // لازم يكون Contractor
        if ($lastLog->fromUser->type !== 'contractor') {
            return false;
        }

        $recipient = $lastLog->fromUser;

        $titleAr = "تم قبول المهمة";
        $titleEn = "Mission Approved";

        $descriptionAr = "تمت الموافقة على تنفيذ المهمة.";
        $descriptionEn = "The mission execution has been approved.";

        $icon = '<svg id="Icon_ionic-ios-checkmark-circle" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <path d="M8 0a8 8 0 1 0 8 8A8.009 8.009 0 0 0 8 0Zm3.707 5.707-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 1 1 1.414-1.414L7 7.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>';

        $color = 'success';

        // contractor route
        $showRoute = route('dashboard.contractor.missions.show', $mission->id);

        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipient->id
        );
    }

    protected function newApprovedConsultantMissionNotification($mission)
    {
        // آخر log من الاستشاري مع status = 4
        $lastLog = $mission->logs()
            ->where('status_id', 4)
            ->with('fromUser')
            ->get()
            ->filter(fn($log) => $log->fromUser && $log->fromUser->type === 'consultant')
            ->last();

        if (!$lastLog || !$lastLog->fromUser) {
            return false;
        }

        $consultantName = $lastLog->fromUser->name;

        // المستلم (Admin اللي أنشأ المهمة)
        $recipient = Admin::find($mission->created_by); // أو حسب حقل createdBy في الـ Mission
        if (!$recipient) {
            return false;
        }

        $titleAr = "تم قبول المهمة من قبل الاستشاري";
        $titleEn = "Mission Approved by Consultant";

        $descriptionAr = "تمت الموافقة على تنفيذ المهمة من قبل الاستشاري: $consultantName.";
        $descriptionEn = "The mission execution has been approved by the consultant: $consultantName.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';

        $color = 'success';
        $showRoute = route('dashboard.admin.missions.show', $mission->id);

        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipient->id
        );
    }

    protected function newCompletedByContractorMissionNotification($mission)
    {
        // 1️⃣ آخر log من المقاول مع status = 20
        $lastLog = $mission->logs()
            ->where('status_id', 20)
            ->with('fromUser')
            ->get()
            ->filter(fn($log) => $log->fromUser && $log->fromUser->type === 'contractor')
            ->last();

        if (!$lastLog || !$lastLog->fromUser) {
            return false; // لو مفيش log صالح
        }

        $contractorName = $lastLog->fromUser->name;

        // 2️⃣ المستلم (Admin أو Consultant اللي لازم يعتمد المهمة)
        // مثال: الاستشاري المسؤول عن المهمة
        $recipient = optional($mission->consultant); // لو عندك علاقة consultant في Mission
        // لو مفيش استشاري، ممكن يظهر للمدير
        if (!$recipient) {
            $recipient = Admin::find($mission->created_by);
        }

        if (!$recipient) {
            return false; // ما فيش مستلم
        }

        // 3️⃣ إعداد عنوان ووصف الإشعار
        $titleAr = "تم تنفيذ المهمة من قبل المقاول";
        $titleEn = "Mission Completed by Contractor";

        $descriptionAr = "قام المقاول $contractorName بتنفيذ المهمة، وهي الآن بانتظار اعتمادك.";
        $descriptionEn = "$contractorName has completed the mission. Waiting for your approval.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';

        $color = 'info';
        $showRoute = route('dashboard.admin.missions.show', $mission->id);

        // 5️⃣ حفظ ودفع الإشعار
        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipient->id
        );
    }

    protected function newStatus4ConsultantNotification($mission)
    {
        // آخر log فيه status = 4 ومن نوع fromUser consultant
        $lastLog = $mission->logs()
            ->where('status_id', 4)
            ->with('fromUser')
            ->get()
            ->filter(fn($log) => $log->fromUser && $log->fromUser->type === 'consultant')
            ->last();

        if (!$lastLog) {
            return false;
        }

        $recipient = $lastLog->fromUser;

        $titleAr = "تمت الموافقة من شركة المقاولات";
        $titleEn = "Approved by Contractor Company";

        $descriptionAr = "تمت الموافقة على تنفيذ المهمة من قبل شركة المقاولات، يرجى مراجعتها واعتمادها.";
        $descriptionEn = "The mission has been approved by the contractor company. Please review and approve it.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = 'info';

        $showRoute = route('dashboard.consultant.missions.show', $mission->id);

        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipient->id
        );
    }

    protected function newRejectedContractorMissionNotification($mission)
    {
        // آخر log فقط
        $lastLog = $mission->logs()
            ->where('status_id', 12)
            ->whereNotNull('result')
            ->latest('id')
            ->first();


        // حماية من null
        if (!$lastLog || !$lastLog->fromUser) {
            return false;
        }

        // لازم يكون Contractor
        if ($lastLog->fromUser->type !== 'contractor') {
            return false;
        }

        $recipient = $lastLog->fromUser;
        $titleAr = "تم رفض المهمة";
        $titleEn = "Mission Rejected";

        $descriptionAr = "تم رفض تنفيذ المهمة.";
        $descriptionEn = "The mission execution has been rejected.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
            </svg>';
        $color = 'danger';

        // تحديد الرابط حسب نوع المستخدم (هنا contractor أكيد)
        $showRoute = route('dashboard.contractor.missions.show', $mission->id);

        return storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipient->id
        );
    }




    protected static function newExtinguishRequestNotification($extinguisher)
    {
        $contractor = auth()->user();

        // عناوين ووصف الإشعار
        $titleAr       = "طلب تقديم إطفاء من " . $contractor->name;
        $titleEn       = "Fire Extinguish Request";
        $descriptionAr = "تم تقديم طلب الإطفاء الرجاء مراجعته.";
        $descriptionEn = "Fire extinguish request has been submitted, please review it.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
        </svg>';
        $color = "info";

        $executiveManager = Admin::where('company_id', $extinguisher->company_id)
            ->whereHas('roles') // فقط التأكد أن له role
            ->orderBy('id', 'asc') // أول مستخدم تم إنشاؤه
            ->first();
        // 2️⃣ تحديد المستلم
        $recipients = $executiveManager?->id;
        // dashboard.contractor.extinguishers.show

        if ($executiveManager->type == 'contractor') {
            $showRoute = route("dashboard.contractor.extinguishers.show", $extinguisher->id);
        }


        // 4️⃣ إرسال الإشعار
        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }

    protected static function newAssignExtinguisherNotification($extinguisher, $assignedUser)
    {
        $assigner = auth()->user();

        $assigned = $assignedUser->id;
        // عناوين ووصف الإشعار
        $titleAr       = "تم تعيينك على مهمة إطفاء";
        $titleEn       = "You Have Been Assigned to Fire Extinguish";
        $descriptionAr = "تم تعيينك من قبل " . $assigner->name . " لمهمة الإطفاء رقم " . $extinguisher->report_number ?? "--";
        $descriptionEn = $assigner->name . " has assigned you to fire extinguish task " . $extinguisher->report_number ?? "--";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
        </svg>';

        $color = "success";


        $showRoute = route("dashboard.contractor.extinguishers.show", $extinguisher->id);


        // إرسال الإشعار
        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $assigned
        );
    }




    protected function newExecutionNotification($extinguisher)
    {
        $admin = auth()->user();

        $titleAr = " تم تنفيذ مهمة الإطفاء بالرجاء المراجعة ";
        $titleEn = "Fire Extinguish Task Completed";
        $descriptionAr = "تم تسليم مهمة الإطفاء رقم {$extinguisher->report_number}";
        $descriptionEn = "Fire extinguish task {$extinguisher->report_number} has been completed";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
        </svg>';
        $color = "success";

        // $hasRole = auth()->user()
        // ->roles()
        // ->where('id', 1)
        // ->get();
        // $admins = \App\Models\Admin::where('type', 'admin')
        // ->whereHas('roles', function ($q) {
        //     $q->where('name_ar', 'مدير تنفيذي'); // role مدير تنفيذي
        // })
        // ->get();

        // $admins = []; // تهيئة المصفوفة


        $manager = Admin::whereHas('roles', function ($q) {
            $q->where('name_ar', 'مدير تنفيذي'); // الاسم باللي موجود عندك في جدول roles
        })->first();




        $showRoute = (route('dashboard.admin.extinguishers.show', $extinguisher->id));


        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $manager
        );
    }






    protected static function newMaterialRequestNotification($materialRequest)
    {


        $materialRequest->load('extinguisher.createdBy');

        // الشخص الذي أنشأ الإطفاء نفسه
        $extinguisherCreator = $materialRequest->extinguisher->createdBy;

        // تحديد الشخص المستلم للإشعار
        // $recipientId = $manager ?? $creator->id;

        $titleAr       = "طلب تقديم صرف مواد للإطفاء " . $materialRequest->title;
        $titleEn       = "Firefighting Materials Disbursement Request ";

        $descriptionAr = "تم تقديم طلب صرف مواد للإطفاء، يرجى مراجعته.";
        $descriptionEn = "A firefighting materials disbursement request has been submitted. Please review it.";



        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                            <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                            <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                            </svg>';

        $color         = "success";


        $showRoute = route("dashboard.admin.withDrawRequests.index", $materialRequest->id);




        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $extinguisherCreator->id
        );
    }

    /* new Mission Material Request Notification   */

    protected static function newMissionMaterialRequestNotification($materialRequest)
    {

        // الشخص الذي أنشأ طلب صرف المواد
        $creatorId = $materialRequest->created_by;

        // أول خطوة: جلب الشخص اللي عين المقاول مباشرة
        $assignerLog = MissionLog::where('to_user_id', $creatorId)->first();

        if ($assignerLog) {
            $directAssignerId = $assignerLog->from_user_id; // الشخص اللي عين المقاول مباشرة

            // الخطوة الثانية: جلب الشخص اللي عين هذا الشخص (الشخص اللي قبله)
            $previousAssignerLog = MissionLog::where('to_user_id', $directAssignerId)->first();

            if ($previousAssignerLog) {
                $recipientId = $previousAssignerLog->from_user_id; // ده الشخص اللي قبله
                $user = \App\Models\Admin::find($recipientId);
                if ($user) {
                    $validUsers = [$recipientId];
                }
            }
        }


        // تحميل بيانات المهمة والشخص الذي أنشأ المهمة
        $materialRequest->load('mission.createdBy');

        // إعداد محتوى الإشعار
        $titleAr       = "طلب تقديم صرف مواد للمهمة " . $materialRequest->title;
        $titleEn       = "Firefighting Materials Disbursement Request";

        $descriptionAr = "تم تقديم طلب صرف مواد للمهمة يرجى مراجعته.";
        $descriptionEn = "A firefighting materials disbursement request has been submitted. Please review it.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';

        $color = "success";

        // $showRoute = route("dashboard.admin.withDrawRequests.index", $materialRequest->id);


        $showRoute = route("dashboard.consultant.withDrawRequests.show", $materialRequest->id);

        // تخزين الإشعار وإرساله للمستخدمين
        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $validUsers
        );
    }



    protected static function newApprovedMaterialRequestNotification($materialRequest)
    {
        $titleAr = "تم قبول طلب صرف من قبل مدير شركة ";
        $titleEn = "The materialRequest has been accepted";
        $descriptionAr = "تمت الموافقة على طلب صرف المواد  الخاصة بك من قبل المدير.";
        $descriptionEn = "Your materialRequest has been approved by the manager";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                        </svg>';

        $color = "success";

        // المستخدم الذي قام بعمل المهمة
        $creator = $materialRequest->createdBy; // تأكد أن لديك علاقة createdBy في الموديل
        $recipients = $creator->id ?? null;


        $showRoute = route("dashboard.admin.withDrawRequests.index", $materialRequest->id);


        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }


    protected static function newRejectedMaterialRequestNotification($materialRequest)
    {
        $titleAr = "تم رفض طلب صرف المواد الخاص بك من قبل  مدير الشركة ";
        $titleEn = "The materialRequest has been rejected";
        $descriptionAr = "تم رفض طلب صرف المواد  الخاصة بك من قبل المدير.";
        $descriptionEn = "Your materialRequest has been rejected by the manager";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                        </svg>';

        $color = "danger"; // عادةً نستخدم danger للرفض

        // المستخدم الذي قام بعمل المهمة
        $creator = $materialRequest->createdBy; // تأكد أن لديك علاقة createdBy في الموديل
        $recipients = $creator->id ?? null;

        // تحديد الرابط حسب نوع المستخدم اللي أنشأ المهمة

        $showRoute = route("dashboard.admin.withDrawRequests.index", $materialRequest->id);


        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipients
        );
    }

    /**
     * إشعارات الموافقة والرفض لطلبات الصرف (WithdrawRequest)
     */
    protected static function sendWithdrawRequestNotification($withDrawRequest, $action, $userType = 'admin')
    {
        $approver = auth()->user();
        $recipientId = $withDrawRequest->created_by;

        if (!$recipientId) {
            return;
        }

        $isApproved = $action === 'approve';

        $titleAr = $isApproved
            ? "تمت الموافقة على طلب الصرف من " . $approver->name
            : "تم رفض طلب الصرف من " . $approver->name;
        $titleEn = $isApproved
            ? "Withdraw Request Approved by " . $approver->name
            : "Withdraw Request Rejected by " . $approver->name;

        $descriptionAr = $isApproved
            ? "تمت الموافقة على طلب الصرف الخاص بك من قبل " . $approver->name
            : "تم رفض طلب الصرف الخاص بك من قبل " . $approver->name . ". يرجى مراجعة السبب.";
        $descriptionEn = $isApproved
            ? "Your withdraw request has been approved by " . $approver->name
            : "Your withdraw request has been rejected by " . $approver->name . ". Please review the reason.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $isApproved ? 'success' : 'danger';

        // تحديد الرابط حسب نوع المستخدم
        if ($userType === 'admin' || $userType === 'amana') {
            $showRoute = route("dashboard.admin.withDrawRequests.show", $withDrawRequest->id);
        } elseif ($userType === 'consultant') {
            $showRoute = route("dashboard.consultant.withDrawRequests.index", $withDrawRequest->id);
        } elseif ($userType === 'contractor') {
            $showRoute = route("dashboard.contractor.withDrawRequests.index", $withDrawRequest->id);
        } else {
            $showRoute = route("dashboard.admin.withDrawRequests.index", $withDrawRequest->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );
    }

    /**
     * إشعارات الموافقة والرفض للمهام (Mission)
     */
    protected static function sendMissionApprovalNotification($mission, $action, $userType = 'admin')
    {
        $approver = auth()->user();
        $recipientId = $mission->created_by ?? $mission->user_id;

        if (!$recipientId) {
            return;
        }

        $isApproved = $action === 'approve';

        $titleAr = $isApproved
            ? "تمت الموافقة على المهمة من " . $approver->name
            : "تم رفض المهمة من " . $approver->name;
        $titleEn = $isApproved
            ? "Mission Approved by " . $approver->name
            : "Mission Rejected by " . $approver->name;

        $descriptionAr = $isApproved
            ? "تمت الموافقة على مهمتك رقم " . ($mission->complaint_number ?? $mission->id) . " من قبل " . $approver->name
            : "تم رفض مهمتك رقم " . ($mission->complaint_number ?? $mission->id) . " من قبل " . $approver->name . ". يرجى مراجعة السبب.";
        $descriptionEn = $isApproved
            ? "Your mission #" . ($mission->complaint_number ?? $mission->id) . " has been approved by " . $approver->name
            : "Your mission #" . ($mission->complaint_number ?? $mission->id) . " has been rejected by " . $approver->name . ". Please review the reason.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $isApproved ? 'success' : 'danger';

        // تحديد الرابط حسب نوع المستخدم
        if ($userType === 'admin' || $userType === 'amana') {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        } elseif ($userType === 'consultant') {
            $showRoute = route("dashboard.consultant.missions.show", $mission->id);
        } elseif ($userType === 'contractor') {
            $showRoute = route("dashboard.contractor.missions.show", $mission->id);
        } else {
            $showRoute = route("dashboard.admin.missions.show", $mission->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );
    }

    /**
     * إشعارات الموافقة والرفض لبنود العقد (ContractItem)
     */
    protected static function sendContractItemApprovalNotification($contractItem, $action, $userType = 'admin')
    {
        $approver = auth()->user();
        $recipientId = $contractItem->created_by ?? $contractItem->contractor_id;

        if (!$recipientId) {
            return;
        }

        $isApproved = $action === 'approve';

        $titleAr = $isApproved
            ? "تمت الموافقة على بند العقد من " . $approver->name
            : "تم رفض بند العقد من " . $approver->name;
        $titleEn = $isApproved
            ? "Contract Item Approved by " . $approver->name
            : "Contract Item Rejected by " . $approver->name;

        $descriptionAr = $isApproved
            ? "تمت الموافقة على بند العقد الخاص بك من قبل " . $approver->name
            : "تم رفض بند العقد الخاص بك من قبل " . $approver->name . ". يرجى مراجعة السبب.";
        $descriptionEn = $isApproved
            ? "Your contract item has been approved by " . $approver->name
            : "Your contract item has been rejected by " . $approver->name . ". Please review the reason.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $isApproved ? 'success' : 'danger';

        // تحديد الرابط حسب نوع المستخدم
        if ($userType === 'admin' || $userType === 'amana') {
            $showRoute = route("dashboard.admin.contractItems.show", $contractItem->id);
        } elseif ($userType === 'consultant') {
            $showRoute = route("dashboard.consultant.contractItems.show", $contractItem->id);
        } elseif ($userType === 'contractor') {
            $showRoute = route("dashboard.contractor.contractItems.show", $contractItem->id);
        } else {
            $showRoute = route("dashboard.admin.contractItems.show", $contractItem->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );
    }


    /**
     * إشعارات الموافقة والرفض للمستخلصات (Abstracte)
     */
    protected static function sendAbstracteApprovalNotification($abstracte, $action, $userType = 'admin')
    {
        $approver = auth()->user();
        $recipientId = $abstracte->created_by;

        if (!$recipientId) {
            return;
        }

        $isApproved = $action === 'approve';

        $titleAr = $isApproved
            ? "تمت الموافقة على المستخلص من " . $approver->name
            : "تم رفض المستخلص من " . $approver->name;
        $titleEn = $isApproved
            ? "Abstracte Approved by " . $approver->name
            : "Abstracte Rejected by " . $approver->name;

        $descriptionAr = $isApproved
            ? "تمت الموافقة على المستخلص " . $abstracte->name . " من قبل " . $approver->name
            : "تم رفض المستخلص " . $abstracte->name . " من قبل " . $approver->name . ". يرجى مراجعة السبب.";
        $descriptionEn = $isApproved
            ? "Abstracte " . $abstracte->name . " has been approved by " . $approver->name
            : "Abstracte " . $abstracte->name . " has been rejected by " . $approver->name . ". Please review the reason.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $isApproved ? 'success' : 'danger';

        // تحديد الرابط حسب نوع المستخدم (Abstracte عادة في admin فقط)
        $showRoute = route("dashboard.admin.abstractes.show", $abstracte->id);

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );
    }

    /**
     * إشعار تعيين المستودع لطلب الصرف
     */
    protected static function sendWarehouseAssignmentNotification($withDrawRequest)
    {
        $withDrawRequest->load('warehouse.manager');

        if (!$withDrawRequest->warehouse || !$withDrawRequest->warehouse->manager) {
            return;
        }

        $warehouse = $withDrawRequest->warehouse;
        $warehouseManager = $warehouse->manager;
        $assigner = auth()->user();

        $titleAr = "تم تعيينك على طلب صرف مواد من المستودع";
        $titleEn = "You have been assigned to a material withdrawal request";

        $descriptionAr = "تم تعيين طلب صرف مواد رقم " . ($withDrawRequest->id ?? '--') . " إلى مستودعك من قبل " . $assigner->name;
        $descriptionEn = "Material withdrawal request #" . ($withDrawRequest->id ?? '--') . " has been assigned to your warehouse by " . $assigner->name;

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = 'info';

        // تحديد الرابط حسب نوع مدير المستودع
        if ($warehouseManager->type == 'admin' || $warehouseManager->type == 'amana') {
            $showRoute = route("dashboard.admin.withDrawRequests.show", $withDrawRequest->id);
        } elseif ($warehouseManager->type == 'consultant') {
            $showRoute = route("dashboard.consultant.withDrawRequests.show", $withDrawRequest->id);
        } elseif ($warehouseManager->type == 'contractor') {
            $showRoute = route("dashboard.contractor.withDrawRequests.show", $withDrawRequest->id);
        } else {
            $showRoute = route("dashboard.admin.withDrawRequests.show", $withDrawRequest->id);
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $warehouseManager->id
        );
    }



    protected static function sendWarehouseManagerNotification($withDrawRequest, $action)
    {
        $warehouseManager = auth()->user();
        $withDrawRequest->load('extinguisher', 'mission'); // لو في علاقات محتاجينها

        // المستلم هو الشخص اللي أنشأ طلب الصرف مباشرة
        $recipientId = $withDrawRequest->created_by; // أو $withDrawRequest->createdBy->id لو عندك علاقة

        if (!$recipientId) {
            return;
        }

        $recipient = Admin::find($recipientId);
        if (!$recipient) {
            return;
        }

        $isApproved = $action === 'accept';

        $titleAr = $isApproved
            ? "تمت الموافقة على طلب الصرف من مدير المستودع"
            : "تم رفض طلب الصرف من مدير المستودع";
        $titleEn = $isApproved
            ? "Withdraw Request Approved by Warehouse Manager"
            : "Withdraw Request Rejected by Warehouse Manager";

        $descriptionAr = $isApproved
            ? "تمت الموافقة على طلب الصرف رقم " . ($withDrawRequest->id ?? '--') . " من قبل مدير المستودع " . $warehouseManager->name
            : "تم رفض طلب الصرف رقم " . ($withDrawRequest->id ?? '--') . " من قبل مدير المستودع " . $warehouseManager->name . ". يرجى مراجعة السبب.";

        $descriptionEn = $isApproved
            ? "Withdraw request #" . ($withDrawRequest->id ?? '--') . " has been approved by warehouse manager " . $warehouseManager->name
            : "Withdraw request #" . ($withDrawRequest->id ?? '--') . " has been rejected by warehouse manager " . $warehouseManager->name . ". Please review the reason.";

        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';
        $color = $isApproved ? 'success' : 'danger';

        // تحديد الرابط حسب نوع المستلم
        if ($recipient->type == 'admin' || $recipient->type == 'amana') {
            $showRoute = route("dashboard.admin.withDrawRequests.show", $withDrawRequest->id);
        } elseif ($recipient->type == 'consultant') {
            $showRoute = route("dashboard.consultant.withDrawRequests.index");
        } elseif ($recipient->type == 'contractor') {
            $showRoute = route("dashboard.contractor.withDrawRequests.index");
        } else {
            $showRoute = route("dashboard.admin.withDrawRequests.index");
        }

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            $recipientId
        );
    }



    // إشعار الموافقة
    protected static function sendExtinguisherApprovalNotification($extinguisher)
    {
        $approver = auth()->user();

        $completedStatusId = Status::where('name_en', 'completed')
            ->orWhere('name_ar', 'مكتمل')
            ->value('id');

        $executorLog = ExtinguisherLog::where('extinguisher_id', $extinguisher->id)
            ->where('status_id', '!=', $completedStatusId)
            ->latest('id')
            ->first();

        $recipientId = $executorLog ? $executorLog->from_user_id : null;

        if (!$recipientId) return;

        $titleAr = "تمت الموافقة على الإطفاء من {$approver->name}";
        $titleEn = "Extinguisher Approved by {$approver->name}";
        $descriptionAr = "تمت الموافقة على الإطفاء {$extinguisher->name}";
        $descriptionEn = "Extinguisher {$extinguisher->name} has been approved.";
        $color = 'success';
        $showRoute = route('dashboard.contractor.extinguishers.show', $extinguisher->id);
        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                    <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                    <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                </svg>';

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            [$recipientId]
        );
    }

    // إشعار الرفض
    protected static function sendExtinguisherRejectionNotification($extinguisher)
    {
        $approver = auth()->user();

        $completedStatusId = Status::where('name_en', 'completed')
            ->orWhere('name_ar', 'مكتمل')
            ->value('id');

        $executorLog = ExtinguisherLog::where('extinguisher_id', $extinguisher->id)
            ->where('status_id', '!=', $completedStatusId)
            ->where('status_id', '!=', Status::where('name_ar', 'مرفوض')->value('id')) // نتجنب الرفض الحالي
            ->latest('id')
            ->first();
        $recipientId = $executorLog ? $executorLog->from_user_id : null;
        // $recipientId = $extinguisher->assigned_to;


        if (!$recipientId) return;

        $titleAr = "تم رفض الإطفاء من {$approver->name}";
        $titleEn = "Extinguisher Rejected by {$approver->name}";
        $descriptionAr = "تم رفض الإطفاء {$extinguisher->name}، يرجى إعادة التنفيذ.";
        $descriptionEn = "Extinguisher {$extinguisher->name} has been rejected. Please re-execute.";
        $color = 'danger';
        $showRoute = route('dashboard.contractor.extinguishers.show', $extinguisher->id);
        $icon = '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
                <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
                <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
                    </svg>';

        storeAndPushNotificationUser(
            $titleAr,
            $titleEn,
            $descriptionAr,
            $descriptionEn,
            $icon,
            $color,
            $showRoute,
            [$recipientId]
        );
    }



    protected static function sendMissionWithdrawRequestNotification($withdrawRequest, $action, $userType = 'admin')
    {
        $approver = auth()->user();

        $recipientId = self::resolveRecipient($withdrawRequest, $action, $userType);
        if (!$recipientId) return;

        $text = self::buildNotificationText($approver, $action);
        $route = self::resolveRoute($withdrawRequest, $userType);

        storeAndPushNotificationUser(
            $text['titleAr'],
            $text['titleEn'],
            $text['descriptionAr'],
            $text['descriptionEn'],
            self::icon(),
            $action === 'approve' ? 'success' : 'danger',
            $route,
            $recipientId
        );
    }
    protected static function resolveRecipient($withdrawRequest, $action, $userType)
    {
        if ($userType === 'admin') {
            return $action === 'approve'
                ? Admin::whereHas('roles', function ($query) {
                    $query->where('name_ar', 'مدير تنفيذي')
                        ->orWhere('name_en', 'super admin');
                })->first()->id
                : $withdrawRequest->created_by;
        }
    }



    protected static function buildNotificationText($approver, $action)
    {
        $isApproved = $action === 'approve';

        return [
            'titleAr' => $isApproved
                ? "تمت الموافقة على طلب الصرف من {$approver->name}"
                : "تم رفض طلب الصرف من {$approver->name}",

            'titleEn' => $isApproved
                ? "Withdraw Request Approved by {$approver->name}"
                : "Withdraw Request Rejected by {$approver->name}",

            'descriptionAr' => $isApproved
                ? "تمت الموافقة على طلب الصرف الخاص بك"
                : "تم رفض طلب الصرف الخاص بك، يرجى مراجعة البيانات",

            'descriptionEn' => $isApproved
                ? "Your withdraw request has been approved"
                : "Your withdraw request has been rejected, please review the data",


        ];
    }

    protected static function resolveRoute($withdrawRequest, $userType)
    {
        return match ($userType) {
            'admin' => route('dashboard.admin.withDrawRequests.show', $withdrawRequest->id),
            'consultant' => route('dashboard.consultant.withDrawRequests.index'),
            'contractor' => route('dashboard.contractor.withDrawRequests.index'),
            default      => route('dashboard.admin.withDrawRequests.show', $withdrawRequest->id),
        };
    }
    protected static function icon()
    {
        return  '<svg id="Icon_ionic-ios-notifications-outline" data-name="Icon ionic-ios-notifications-outline" xmlns="http://www.w3.org/2000/svg" width="14.381" height="18" viewBox="0 0 14.381 18">
        <path id="Path_19090" data-name="Path 19090" d="M18.328,28.336a.583.583,0,0,0-.571.459,1.127,1.127,0,0,1-.225.49.85.85,0,0,1-.724.265.864.864,0,0,1-.724-.265,1.127,1.127,0,0,1-.225-.49.583.583,0,0,0-.571-.459h0a.587.587,0,0,0-.571.715,2.01,2.01,0,0,0,2.092,1.669A2.006,2.006,0,0,0,18.9,29.051a.589.589,0,0,0-.571-.715Z" transform="translate(-9.63 -12.72)" fill="black"/>
        <path id="Path_19091" data-name="Path 19091" d="M20.975,17.261c-.693-.913-2.056-1.449-2.056-5.538,0-4.2-1.854-5.885-3.581-6.289-.162-.04-.279-.094-.279-.265v-.13A1.1,1.1,0,0,0,13.98,3.93h-.027a1.1,1.1,0,0,0-1.08,1.107v.13c0,.166-.117.225-.279.265-1.732.409-3.581,2.092-3.581,6.289,0,4.089-1.363,4.62-2.056,5.538a.893.893,0,0,0,.715,1.431h12.6A.894.894,0,0,0,20.975,17.261Zm-1.755.261H8.729a.2.2,0,0,1-.148-.328,5.45,5.45,0,0,0,.945-1.5,10.2,10.2,0,0,0,.643-3.968,6.9,6.9,0,0,1,.94-3.905A2.887,2.887,0,0,1,12.85,6.576a1.577,1.577,0,0,0,.837-.472.356.356,0,0,1,.535-.009,1.63,1.63,0,0,0,.846.481,2.887,2.887,0,0,1,1.741,1.242,6.9,6.9,0,0,1,.94,3.905,10.2,10.2,0,0,0,.643,3.968,5.512,5.512,0,0,0,.967,1.525A.186.186,0,0,1,19.221,17.522Z" transform="translate(-6.775 -3.93)" fill="black"/>
    </svg>';
    }
}
