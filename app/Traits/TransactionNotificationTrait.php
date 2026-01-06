<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Transaction;
use App\Notifications\NotificationApi;
use App\Notifications\transaction\PaymentFailedNotification;
use App\Notifications\transaction\AmountReceivedNotification;
use App\Notifications\transaction\PaymentPendingNotification;
use App\Notifications\transaction\PayoutRejectedNotification;
use App\Notifications\transaction\PayoutCompletedNotification;
use App\Notifications\transaction\PayoutRequestedNotification;
use App\Notifications\transaction\PaymentCompletedNotification;



trait TransactionNotificationTrait
{


    protected static function notifyPaymentCompleted($transaction)
    {
        // $descriptionTransaction = substr($transaction->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "تم استلام المبلغ بنجاح",
            'en' => "Your payment has been received successfully"
        ]);

        $description = json_encode([
            'ar' => "مبروك تم استلام المبلغ الخاص بك",
            'en' => "Your payment has been received successfully"
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Delivered_Wallet';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $transaction->user;

        if (!is_null($user->fcm_token)) {
            // sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }
        $user->notify($notification);
    }




    protected static function notifyPaymentFailed($transaction)
    {
        // $descriptionTransaction = substr($transaction->description, 0, 50) . ' ...';
        $title = json_encode([
            'ar' => "عذرا, فشل استلام المبلغ",
            'en' => "Your Payment Has Failed"
        ]);

        $description = json_encode([
            'ar' => "عذرا, فشل استلام المبلغ الخاص بك",
            'en' => "Your Payment Has Failed"
        ]);
        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Failed_Wallet';
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $transaction->user;

        if (!is_null($user->fcm_token)) {
            // sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }
        $user->notify($notification);
    }

    protected function notifyPaymentPending($offer)
    {
        // محتوى العنوان متعدد اللغات
        $title = json_encode([
            'ar' => "تم بدء عملية الدفع",
            'en' => "Payment Started"
        ]);

        // محتوى الوصف متعدد اللغات
        $description = json_encode([
            'ar' => "تم بدء عملية الدفع لعرضك وهو الآن قيد المراجعة (Pending).",
            'en' => "The payment for your offer has started and is now pending."
        ]);

        $date = \Carbon\Carbon::now()->diffForHumans();
        $type = 'Payment Pending';

        // ممكن نرسل بيانات إضافية زي رقم العرض والمهمة
        $extraData = [
            'offer_id'   => $offer->id,
            'mission_id' => $offer->mission_id,
            'amount'     => $offer->price ?? null
        ];

        // كائن الإشعار
        $notification = new NotificationApi($title, $description, $date, $type, $extraData);

        $title = json_decode($title);
        $lang  = app()->getLocale();

        // إرسال عبر Firebase (لو فيه fcm_token)
        if (!is_null($offer->user->fcm_token)) {
            // sendFirebaseNotification($title->$lang, $type, $offer->user->fcm_token, $extraData);
        }

        // إشعار داخلي (Laravel Notifications)
        $offer->user->notify($notification);
    }


    protected static function notifyTransactionDisputed($transaction)
    {
        // محتوى العنوان متعدد اللغات
        $title = json_encode([
            'ar' => "عملية الدفع قيد النزاع",
            'en' => "Your payment is disputed"
        ]);

        // محتوى الوصف متعدد اللغات
        $description = json_encode([
            'ar' => "عملية الدفع الخاصة بك ما زالت قيد النزاع",
            'en' => "Your payment is still under dispute"
        ]);

        $date         = \Carbon\Carbon::now()->diffForHumans();
        $type         = 'Payment Disputed'; // نوع الإشعار
        $notification = new NotificationApi($title, $description, $date, $type);
        $title        = json_decode($title);
        $lang         = app()->getLocale();

        $user = $transaction->user;

        if (!is_null($user->fcm_token)) {
            // تفعيل الإرسال عبر Firebase
            // sendFirebaseNotification($title->$lang, $type, $user->fcm_token);
        }

        // إشعار داخلي عبر Laravel Notifications
        $user->notify($notification);
    }
}
