<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Dashboard\PaymentCheckoutService;
use App\Services\OnlinePayService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OnlinePaymentController extends Controller
{
    public function __construct(protected PaymentCheckoutService $checkout) {}

    // بدء دفع أونلاين
    public function checkout(Request $request, Payment $payment, OnlinePayService $pay)
    {
        $request->validate(['provider' => ['required', 'in:paymob,fawry']]);
        Gate::forUser(auth('admin')->user())->authorize('pay', $payment);

        return view('dashboard.payments.checkout', $this->checkout->checkoutData($payment, $request->provider));
    }

    // callback من البوابة (تحقق HMAC ثم تأكيد)
    public function callback(Request $request, string $provider, OnlinePayService $pay)
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

    // تأكيد الدفع — خطوة 1: رفع الإيصال + إرسال OTP واتساب لرقم الدافع
    public function confirm(Request $request, Payment $payment)
    {
        Gate::forUser(auth('admin')->user())->authorize('pay', $payment);
        $me = auth('admin')->user();

        // إعادة إرسال: لو في طلب معلق (مبلغ + إيصال محفوظين) مش لازم رفع من جديد
        $pending = \Illuminate\Support\Facades\Cache::get("pay_otp:{$payment->id}:{$me->id}");
        if (! $request->hasFile('receipt') && $pending) {
            $this->checkout->sendOtp($payment, $pending['amount'], $me, $pending['receipt'] ?? null);

            return view('dashboard.payments.otp', compact('payment'))->with('success', __('تم إرسال كود جديد'));
        }

        $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:1', 'max:' . max(1, (float) $payment->remaining)],
            'receipt' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        if (! $me->phone) {
            return back()->with('error_message', __('ضيف رقم موبايلك في البروفايل الأول عشان يوصلك كود التحقق'));
        }

        $receiptPath = 'storage/' . $request->file('receipt')->store('pay_receipts', 'public');
        $this->checkout->sendOtp($payment, (float) $request->paid_amount, $me, $receiptPath);

        return view('dashboard.payments.otp', compact('payment'));
    }

    // تأكيد الدفع — خطوة 2: التحقق من OTP ثم التسجيل
    public function verifyOtp(Request $request, Payment $payment)
    {
        Gate::forUser(auth('admin')->user())->authorize('pay', $payment);
        $request->validate(['code' => ['required', 'digits:6']]);
        $check = $this->checkout->checkOtp($payment, $request->code, auth('admin')->user());

        if (! $check['ok']) {
            return back()->with('error_message', $check['error']);
        }

        $this->checkout->applyPayment($payment, $check['amount'], auth('admin')->user(), $check['receipt'] ?? null);

        return redirect()->route('admin.onlinepay.receipt', $payment->id)->with('success', __('تم تأكيد الدفع — بانتظار اعتماد المدرس'));
    }

    // اعتماد/رفض الإيصال (مدرس/إدارة)
    public function review(Request $request, Payment $payment)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $request->validate(['decision' => ['required', 'in:approve,reject']]);
        if ($request->decision === 'approve') {
            $this->checkout->approve($payment, auth('admin')->user());

            return back()->with('success', __('تم اعتماد الدفعة'));
        }
        $this->checkout->reject($payment);

        return back()->with('success', __('تم رفض الإيصال وإرجاع المبلغ'));
    }

    // إيصال PDF
    public function receipt(Payment $payment)
    {
        Gate::forUser(auth('admin')->user())->authorize('view', $payment);
        $payment->loadMissing(['student']);
        $html = view('dashboard.payments.receipt-pdf', compact('payment'))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $path = "receipts/receipt-{$payment->id}.pdf";
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $mpdf->Output('', 'S'));
        $payment->update(['receipt_path' => $path]);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=receipt-{$payment->id}.pdf",
        ]);
    }
}
