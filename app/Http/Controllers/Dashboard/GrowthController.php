<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\StudentPoint;
use App\Models\Wallet;
use Illuminate\Http\Request;

// كوبونات + إحالات + محافظ
class GrowthController extends Controller
{
    // استخدام كوبون على فاتورة
    public function applyCoupon(Request $request, Payment $payment)
    {
        $request->validate(['code' => ['required', 'string']]);
        $coupon = Coupon::where('code', $request->code)->first();
        if (! $coupon || ! $coupon->isValid()) {
            return back()->with('error_message', __('كود غير صالح أو منتهي'));
        }
        $discount = $coupon->discountFor((float) $payment->amount);
        $payment->update(['amount' => max(0, (float) $payment->amount - $discount)]);
        $coupon->increment('used_count');

        return back()->with('success', __('تم تطبيق خصم') . ': ' . $discount . ' ج');
    }

    // تسجيل إحالة: طالب يدعو طالب (بكود = id الداعي)
    public function refer(Request $request)
    {
        $request->validate(['referrer_id' => ['required', 'exists:admins,id']]);
        $user = auth('admin')->user();
        abort_unless($user->type === 'student', 403);
        $referrer = Admin::findOrFail($request->referrer_id);
        abort_if($referrer->id === $user->id, 422);

        $ref = Referral::firstOrCreate(
            ['referrer_id' => $referrer->id, 'referred_id' => $user->id],
            ['bonus_points' => 20, 'awarded' => false]
        );
        if (! $ref->awarded) {
            StudentPoint::create(['student_id' => $referrer->id, 'points' => $ref->bonus_points, 'reason' => 'referral']);
            $ref->update(['awarded' => true]);
        }

        return back()->with('success', __('تم تسجيل الإحالة ومنح النقاط'));
    }

    // محفظتي
    public function wallet()
    {
        $wallet = Wallet::for(auth('admin')->user());
        $txs = $wallet->transactions()->latest()->limit(30)->get();

        return view('dashboard.growth.wallet', compact('wallet', 'txs'));
    }

    // عمولة مدرس عند تحصيل فاتورة طالب في صفه
    public static function teacherCommission(Payment $payment, float $rate = 0.10): void
    {
        $student = $payment->student;
        if (! $student) {
            return;
        }
        $teacherId = \App\Models\Course::where('grade', $student->grade)->value('teacher_id')
            ?? \App\Models\Video::where('grade', $student->grade)->value('teacher_id');
        if (! $teacherId) {
            return;
        }
        $teacher = Admin::find($teacherId);
        if ($teacher) {
            Wallet::for($teacher)->credit((float) $payment->paid_amount * $rate, 'commission:' . $payment->id);
        }
    }
}
