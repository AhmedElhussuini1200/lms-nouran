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

    public function sendOtp(Payment $payment, float $amount, $user): void
    {
        // ثابت للتجربة حالياً — يتبدل بكود عشوائي في الإنتاج
        $code = '123456';
        Cache::put($this->otpKey($payment, $user), [
            'hash' => hash('sha256', $code),
            'amount' => $amount,
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

        return ['ok' => true, 'amount' => $data['amount']];
    }

    public function applyPayment(Payment $payment, float $amount, $user): Payment
    {
        $payment->update([
            'paid_amount' => $payment->paid_amount + $amount,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'payer_name' => $user->name,
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
}
