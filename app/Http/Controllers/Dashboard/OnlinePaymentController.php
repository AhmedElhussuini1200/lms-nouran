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

        return view('dashboard.payments.checkout', compact('payment', 'provider', 'gatewayUrl'));
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

    // تأكيد الدفع (callback من البوابة أو تأكيد يدوي)
    public function confirm(Request $request, Payment $payment, WhatsappService $whatsapp)
    {
        $request->validate(['paid_amount' => ['required', 'numeric', 'min:1']]);
        $payment->update([
            'paid_amount' => $payment->paid_amount + $request->paid_amount,
            'paid_at' => now(),
        ]);

        // عمولة المدرس على التحصيل
        \App\Http\Controllers\Dashboard\GrowthController::teacherCommission($payment->fresh());

        foreach ($payment->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, __('تم استلام دفعة'), $payment->student->name . ' - ' . $request->paid_amount . ' ج', 'success', route('admin.payments.index'));
            $whatsapp->send($parent->phone ?? '', __('تم استلام دفعة') . ': ' . $request->paid_amount . ' ج - ' . __('المرجع') . ': ' . ($payment->transaction_ref ?? '-'), $parent->whatsapp_key ?? '', $parent->id);
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
