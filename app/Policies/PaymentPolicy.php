<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Payment;

class PaymentPolicy
{
    // الطالب فاتورته — ولي الأمر فواتير أبنائه — الإدارة الكل
    public function pay(Admin $user, Payment $payment): bool
    {
        if (in_array($user->type, ['admin', 'teacher'])) {
            return true;
        }
        if ($user->type === 'student') {
            return $payment->student_id === $user->id;
        }
        if ($user->type === 'parent') {
            return $user->students()->where('admins.id', $payment->student_id)->exists();
        }

        return false;
    }

    public function view(Admin $user, Payment $payment): bool
    {
        return $this->pay($user, $payment);
    }
}
