<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class OnlinePaymentController extends Controller
{
    // بدء دفع أونلاين (حقيقي عند وجود المفاتيح — وإلا mock)
    public function checkout(Request $request, Payment $payment, \App\Services\OnlinePayService $pay)
    {
        $request->validate(['provider' => ['required', 'in:paymob,fawry']]);
        $provider = $request->provider;

        $realUrl = $provider === 'paymob' ? $pay->paymobIntention($payment) : null;
        $fawry = $provider === 'fawry' ? $pay->fawryCharge($payment) : null;

        $ref = strtoupper($provider) . '-' . date('Ym') . '-' . $payment->id . '-' . rand(1000, 9999);
        $payment->update(['provider' => $provider, 'transaction_ref' => $ref]);

        // رابط الدفع الحقيقي إن توفر
        $gatewayUrl = $realUrl ?? ($fawry['paymentUrl'] ?? null);

        // المدرس المستلم: مدرس صف الطالب (تدفع لمين؟)
        $payment->loadMissing('student');
        $teacher = \App\Models\Course::where('grade', $payment->student->grade ?? null)
            ->with('teacher:id,name,brand_name,pay_methods,pay_details,monthly_classes,price_per_class')
            ->latest()->first()?->teacher;

        return view('dashboard.payments.checkout', compact('payment', 'provider', 'gatewayUrl', 'teacher'));
    }

    // callback من البوابة (تحقق HMAC ثم تأكيد)
    public function callback(Request $request, string $provider, \App\Services\OnlinePayService $pay, \App\Services\WhatsappService $whatsapp)
    {
        abort_unless(in_array($provider, ['paymob', 'fawry']), 404);
        if (! $pay->verifyCallback($provider, $request->all())) {
            return response()->json(['ok' => false], 403);
        }
        $payment = Payment::where('transaction_ref', 'like', strtoupper($provider) . '%')
            ->latest()->firstOrFail();
        $amount = (float) ($request->input('obj.amount_cents', 0) / 100 ?: $request->input('orderAmount', 0));
        if ($amount > 0) {
            $payment->update(['paid_amount' => $payment->paid_amount + $amount, 'paid_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    // تأكيد الدفع — خطوة 1: إرسال OTP واتساب لرقم الدافع
    public function confirm(Request $request, Payment $payment, WhatsappService $whatsapp)
    {
        $request->validate(['paid_amount' => ['required', 'numeric', 'min:1', 'max:' . max(1, (float) $payment->remaining)]]);
        $me = auth('admin')->user();

        // صلاحية: الطالب نفسه أو ولي أمره فقط
        abort_unless(
            ($me->type === 'student' && $payment->student_id === $me->id) ||
            ($me->type === 'parent' && $me->students()->where('admins.id', $payment->student_id)->exists()) ||
            in_array($me->type, ['admin', 'teacher']),
            403
        );

        if (! $me->phone) {
            return back()->with('error_message', __('ضيف رقم موبايلك في البروفايل الأول عشان يوصلك كود التحقق'));
        }

        // ثابت للتجربة حالياً — يتبدل بكود عشوائي في الإنتاج
        $code = '123456';
        $key = "pay_otp:{$payment->id}:{$me->id}";
        \Illuminate\Support\Facades\Cache::put($key, ['hash' => hash('sha256', $code), 'amount' => (float) $request->paid_amount, 'tries' => 0], now()->addMinutes(5));

        $whatsapp->send(
            $me->phone,
            __('كود تأكيد الدفع') . ': ' . $code . ' — ' . $request->paid_amount . ' ج (' . __('صالح 5 دقائق') . ')',
            $me->whatsapp_key ?? '',
            $me->id
        );

        return view('dashboard.payments.otp', compact('payment'));
    }

    // تأكيد الدفع — خطوة 2: التحقق من OTP ثم التسجيل
    public function verifyOtp(Request $request, Payment $payment, WhatsappService $whatsapp)
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $me = auth('admin')->user();
        $key = "pay_otp:{$payment->id}:{$me->id}";
        $data = \Illuminate\Support\Facades\Cache::get($key);

        if (! $data) {
            return back()->with('error_message', __('الكود انتهت صلاحيته — اطلب كود جديد'));
        }
        if (($data['tries'] ?? 0) >= 5) {
            \Illuminate\Support\Facades\Cache::forget($key);

            return back()->with('error_message', __('محاولات كتير غلط — اطلب كود جديد'));
        }
        if (! hash_equals($data['hash'], hash('sha256', $request->code))) {
            $data['tries']++;
            \Illuminate\Support\Facades\Cache::put($key, $data, now()->addMinutes(5));

            return back()->with('error_message', __('الكود غلط — فاضل') . ' ' . (5 - $data['tries']) . ' ' . __('محاولات'));
        }
        \Illuminate\Support\Facades\Cache::forget($key);

        $payment->update([
            'paid_amount' => $payment->paid_amount + $data['amount'],
            'paid_at' => now(),
            // تسجيل الدافع الفعلي (ولي الأمر غالباً) — الفاتورة تفضل باسم الطالب
            'paid_by' => $me->id,
            'payer_name' => $me->name,
        ]);

        // عمولة المدرس على التحصيل
        \App\Http\Controllers\Dashboard\GrowthController::teacherCommission($payment->fresh());

        foreach ($payment->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, __('تم استلام دفعة'), $payment->student->name . ' - ' . $data['amount'] . ' ج', 'success', route('admin.payments.index'));
            $whatsapp->send($parent->phone ?? '', __('تم استلام دفعة') . ': ' . $data['amount'] . ' ج - ' . __('المرجع') . ': ' . ($payment->transaction_ref ?? '-'), $parent->whatsapp_key ?? '', $parent->id);
        }

        return redirect()->route('admin.onlinepay.receipt', $payment->id)->with('success', __('تم تأكيد الدفع'));
    }

    // إيصال PDF
    public function receipt(Payment $payment)
    {
        $payment->loadMissing(['student']);
        $html = view('dashboard.payments.receipt-pdf', compact('payment'))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $path = "receipts/receipt-{$payment->id}.pdf";
        \Storage::disk('public')->put($path, $mpdf->Output('', 'S'));
        $payment->update(['receipt_path' => $path]);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=receipt-{$payment->id}.pdf",
        ]);
    }
}
