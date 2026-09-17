<?php

namespace App\Services\Dashboard;

use App\Models\Course;
use App\Models\Payment;
use App\Services\OnlinePayService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * منطق الدفع أونلاين: checkout + OTP + اعتماد — الكنترولر رفيع.
 */
class PaymentCheckoutService
{
    public function __construct(
        protected OnlinePayService $gateway,
        protected WhatsappService $whatsapp,
    ) {}

    public function checkoutData(Payment $payment, string $provider): array
    {
        $realUrl = $provider === 'paymob' ? $this->gateway->paymobIntention($payment) : null;
        $fawry = $provider === 'fawry' ? $this->gateway->fawryCharge($payment) : null;

        $payment->update([
            'provider' => $provider,
            'transaction_ref' => strtoupper($provider) . '-' . date('Ym') . '-' . $payment->id . '-' . random_int(1000, 9999),
        ]);

        $payment->loadMissing('student');
        $teacher = Course::where('grade', $payment->student->grade ?? null)
            ->with('teacher:id,name,brand_name,pay_methods,pay_details,monthly_classes,price_per_class')
            ->latest()->first()?->teacher;

        return [
            'payment' => $payment,
            'provider' => $provider,
            'gatewayUrl' => $realUrl ?? ($fawry['paymentUrl'] ?? null),
            'teacher' => $teacher,
        ];
    }

    public function sendOtp(Payment $payment, float $amount, $user, ?string $receiptPath = null): void
    {
        // ثابت للتجربة حالياً — يتبدل بكود عشوائي في الإنتاج
        $code = '123456';
        Cache::put($this->otpKey($payment, $user), [
            'hash' => hash('sha256', $code),
            'amount' => $amount,
            'receipt' => $receiptPath,
            'tries' => 0,
        ], now()->addMinutes(5));

        $this->whatsapp->send(
            $user->phone,
            __('كود تأكيد الدفع') . ': ' . $code . ' — ' . $amount . ' ج (' . __('صالح 5 دقائق') . ')',
            $user->whatsapp_key ?? '',
            $user->id
        );
    }

    /** @return array{ok: bool, error?: string, remaining?: int} */
    public function checkOtp(Payment $payment, string $code, $user): array
    {
        $key = $this->otpKey($payment, $user);
        $data = Cache::get($key);
        if (! $data) {
            return ['ok' => false, 'error' => __('الكود انتهت صلاحيته — اطلب كود جديد')];
        }
        if (($data['tries'] ?? 0) >= 5) {
            Cache::forget($key);

            return ['ok' => false, 'error' => __('محاولات كتير غلط — اطلب كود جديد')];
        }
        if (! hash_equals($data['hash'], hash('sha256', $code))) {
            $data['tries']++;
            Cache::put($key, $data, now()->addMinutes(5));

            return ['ok' => false, 'error' => __('الكود غلط — فاضل') . ' ' . (5 - $data['tries']) . ' ' . __('محاولات')];
        }
        Cache::forget($key);

        return ['ok' => true, 'amount' => $data['amount'], 'receipt' => $data['receipt'] ?? null];
    }

    public function applyPayment(Payment $payment, float $amount, $user, ?string $receipt = null): Payment
    {
        $payment->update([
            'paid_amount' => $payment->paid_amount + $amount,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'payer_name' => $user->name,
            'receipt_image' => $receipt ?? $payment->receipt_image,
            'unverified_amount' => $amount,
            'receipt_verified' => false,
            'verified_by' => null,
        ]);

        \App\Http\Controllers\Dashboard\GrowthController::teacherCommission($payment->fresh());

        $payment->loadMissing('student.parents');
        foreach ($payment->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, __('تم استلام دفعة'), $payment->student->name . ' - ' . $amount . ' ج', 'success', route('admin.payments.index'));
            $this->whatsapp->send(
                $parent->phone ?? '',
                __('تم استلام دفعة') . ': ' . $amount . ' ج - ' . __('المرجع') . ': ' . ($payment->transaction_ref ?? '-'),
                $parent->whatsapp_key ?? '',
                $parent->id
            );
        }

        return $payment;
    }

    protected function otpKey(Payment $payment, $user): string
    {
        return "pay_otp:{$payment->id}:{$user->id}";
    }

    // اعتماد الإيصال من المدرس/الإدارة
    public function approve(Payment $payment, $user): Payment
    {
        $payment->update(['receipt_verified' => true, 'verified_by' => $user->id, 'unverified_amount' => 0]);
        $payment->loadMissing('student');
        notifyAdmin($payment->student_id, __('تم اعتماد دفعتك'), $payment->month . ' - ' . $payment->paid_amount . ' ج', 'success', route('admin.payments.index'));

        return $payment;
    }

    // رفض الإيصال: يخصم المبلغ المعلق فقط ويمسح الإيصال
    public function reject(Payment $payment): Payment
    {
        $payment->update([
            'paid_amount' => max(0, (float) $payment->paid_amount - (float) $payment->unverified_amount),
            'receipt_image' => null,
            'unverified_amount' => 0,
            'receipt_verified' => false,
            'verified_by' => null,
        ]);
        $payment->loadMissing('student');
        notifyAdmin($payment->student_id, __('تم رفض إيصال الدفع'), $payment->month . ' - ' . __('أعد رفع إيصال صحيح'), 'warning', route('admin.payments.index'));

        return $payment;
    }
}
