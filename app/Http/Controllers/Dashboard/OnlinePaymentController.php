<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class OnlinePaymentController extends Controller
{
    // بدء دفع أونلاين (mock جاهز للربط بـ Paymob/Fawry)
    public function checkout(Request $request, Payment $payment)
    {
        $request->validate(['provider' => ['required', 'in:paymob,fawry']]);
        $provider = $request->provider;

        // TODO: ربط مفاتيح Paymob/Fawry من .env ثم استبدال هذا الـ mock
        $ref = strtoupper($provider) . '-' . date('Ym') . '-' . $payment->id . '-' . rand(1000, 9999);
        $payment->update(['provider' => $provider, 'transaction_ref' => $ref]);

        // رابط الدفع (حالياً صفحة تأكيد داخلية لحين تفعيل المفاتيح)
        return view('dashboard.payments.checkout', compact('payment', 'provider'));
    }

    // تأكيد الدفع (callback من البوابة أو تأكيد يدوي)
    public function confirm(Request $request, Payment $payment, WhatsappService $whatsapp)
    {
        $request->validate(['paid_amount' => ['required', 'numeric', 'min:1']]);
        $payment->update([
            'paid_amount' => $payment->paid_amount + $request->paid_amount,
            'paid_at' => now(),
        ]);

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
