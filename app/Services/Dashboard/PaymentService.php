<?php

namespace App\Services\Dashboard;

use App\Models\Admin;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use App\Repositories\Dashboard\Contracts\PaymentRepositoryInterface;

class PaymentService
{
    protected $paymentRepository;
    protected $whatsapp;

    public function __construct(PaymentRepositoryInterface $paymentRepository, WhatsappService $whatsapp)
    {
        $this->paymentRepository = $paymentRepository;
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request)
    {
        $user = auth('admin')->user();
        abort_unless(in_array($user->type, ['admin', 'teacher', 'student', 'parent']), 403);

        $payments = $this->paymentRepository->index($request);
        $summary = in_array($user->type, ['admin', 'teacher'])
            ? $this->paymentRepository->monthlySummary($request->get('year', date('Y')))
            : null;

        return view('dashboard.payments.index', compact('payments', 'summary'));
    }

    public function create()
    {
        $this->authorizeManage();
        $students = Admin::where('type', 'student')->orderBy('name')->get(['id', 'name', 'grade']);
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.payments.create', compact('students', 'grades'));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $request->validate([
            'student_id' => ['required', 'exists:admins,id'],
            'month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'method' => ['nullable', 'in:cash,vodafone,instapay,card'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['created_by'] = auth('admin')->id();
        // لو في مبلغ مدفوع مع الإنشاء: الدافع هو منشئ الفاتورة ما لم يُذكر اسم آخر
        if ((float) $data['paid_amount'] > 0 && empty($data['paid_by'] ?? null)) {
            $data['paid_by'] = auth('admin')->id();
            $data['payer_name'] = $data['payer_name'] ?? auth('admin')->user()->name;
        }

        // منع الفاتورة المكررة برسالة واضحة بدل خطأ SQL
        $exists = Payment::where('student_id', $data['student_id'])->where('month', $data['month'])->first();
        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => __('توجد فاتورة لهذا الطالب في نفس الشهر'),
                    'errors' => ['month' => [__('توجد فاتورة لهذا الطالب في نفس الشهر — تم فتحها للتعديل')]],
                    'url' => route('admin.payments.edit', $exists->id),
                ], 422);
            }

            return redirect()->route('admin.payments.edit', $exists->id)
                ->with('success', __('توجد فاتورة لهذا الطالب في نفس الشهر — تم فتحها للتعديل'));
        }

        $payment = $this->paymentRepository->store($data);
        $this->notifyPayment($payment, __('فاتورة شهر جديد'));

        if ($request->ajax()) {
            return response()->json(['message' => __('تم إنشاء الفاتورة'), 'url' => route('admin.payments.index', ['month' => $payment->month])]);
        }

        return redirect()->route('admin.payments.index', ['month' => $payment->month])->with('success', __('تم إنشاء الفاتورة'));
    }

    public function edit(Payment $payment)
    {
        $this->authorizeManage();

        return view('dashboard.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorizeManage();
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'method' => ['nullable', 'in:cash,vodafone,instapay,card'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // الدافع: اللي زوّد المدفوع — لو ولي أمر/طالب دفع بنفسه يتسجل هو
        $oldPaid = (float) $payment->paid_amount;
        $newPaid = (float) ($data['paid_amount'] ?? $oldPaid);
        $me = auth('admin')->user();
        if ($newPaid > $oldPaid && in_array($me->type, ['parent', 'student'])) {
            $data['paid_by'] = $me->id;
            $data['payer_name'] = $me->name;
        } elseif (! empty($data['payer_name']) && empty($payment->paid_by)) {
            $data['paid_by'] = $me->id;
        }
        // تحصيل الإدارة/المدرس نقداً = موثّق تلقائياً
        if (in_array($me->type, ['admin', 'teacher'])) {
            $data['receipt_verified'] = true;
            $data['unverified_amount'] = 0;
        }

        $this->paymentRepository->update($data, $payment);
        $this->notifyPayment($payment->fresh(), __('تحديث مدفوعات'));

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الفاتورة'), 'url' => route('admin.payments.index', ['month' => $payment->month])]);
        }

        return redirect()->route('admin.payments.index', ['month' => $payment->month])->with('success', __('تم تحديث الفاتورة'));
    }

    public function destroy(Request $request, Payment $payment)
    {
        $this->authorizeManage();
        $this->paymentRepository->destroy($payment);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الفاتورة'), 'url' => route('admin.payments.index')]);
        }

        return redirect()->route('admin.payments.index')->with('success', __('تم حذف الفاتورة'));
    }

    public function monthlyPdf(Request $request)
    {
        $this->authorizeManage();
        $month = $request->get('month', date('Y-m'));
        $payments = Payment::with(['student:id,name,grade', 'status'])->where('month', $month)->orderBy('student_id')->get();

        $html = view('dashboard.payments.monthly-pdf', compact('payments', 'month'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=payments-{$month}.pdf",
        ]);
    }

    protected function authorizeManage(): void
    {
        // الحسابات المالية للأدمن فقط — منعاً لتضارب المدرسين
        abort_unless(auth('admin')->user()->type === 'admin', 403);
    }

    protected function notifyPayment(Payment $payment, string $title): void
    {
        $payment->loadMissing(['student.parents', 'status']);
        $msg = $payment->month . ' - ' . __('المطلوب') . ': ' . $payment->amount . ' - ' . __('المدفوع') . ': ' . $payment->paid_amount;
        $link = route('admin.payments.index', ['month' => $payment->month]);
        notifyAdmin($payment->student_id, $title, $msg, 'info', $link);
        foreach ($payment->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, $title . ' - ' . $payment->student->name, $msg, 'warning', $link);
            // واتساب ولي الأمر بالفاتورة والتحصيل
            $this->whatsapp->send(
                $parent->phone ?? '',
                $title . ' - ' . $payment->student->name . ' - ' . $msg,
                $parent->whatsapp_key ?? '',
                $parent->id
            );
        }
    }
}
