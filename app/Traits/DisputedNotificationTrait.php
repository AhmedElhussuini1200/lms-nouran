<?php

namespace App\Traits;

use App\Notifications\Dispute\TransactionDisputedNotification;
use App\Notifications\Dispute\TransactionRefundedNotification;
use App\Notifications\Dispute\MissionDisputedNotification;
use App\Notifications\Dispute\MissionResolvedNotification;
use App\Notifications\Dispute\TransactionResolvedNotification;




trait DisputedNotificationTrait
{
    //
    // protected function notifyTransactionDisputed($transaction)
    // {
    //     $transaction->user?->notify(new TransactionDisputedNotification($transaction));
    // }
    // protected function notifyTransactionRefunded($transaction)
    // {
    //     $transaction->user?->notify(new TransactionRefundedNotification($transaction));
    // }


    // protected function notifyTransactionResolved($transaction, $winner)
    // {
    //     // إشعار إنه النزاع اتحسم لصالح طرف
    //     $winner->notify(new TransactionResolvedNotification($transaction));
    // }
    // //اشعار إن المهمة اتحسمت للنزاع
    // protected function notifyMissionDisputed($mission)
    // {
    //     $mission->user?->notify(new MissionDisputedNotification($mission));
    // }
    // // إشعار إن المهمة اتحسمت لصالح طرف
    // protected function notifyMissionResolved($mission, $winner)
    // {
    //     $winner->notify(new MissionResolvedNotification($mission));
    // }
}
