<!DOCTYPE html><html dir="rtl"><head><meta charset="utf-8"><style>body{font-family:dejavusans}</style></head>
<body><h2>إيصال سداد 🧾</h2><p>الطالب (المستفيد): {{ $payment->student->name ?? '' }}</p>
<p>الدافع: {{ $payment->payer_label }}</p>
<p>الشهر: {{ $payment->month }}</p><p>المدفوع: {{ $payment->paid_amount }} ج</p>
<p>المرجع: {{ $payment->transaction_ref ?? '-' }} ({{ $payment->provider ?? 'نقدي' }})</p>
<p>التاريخ: {{ $payment->paid_at ?? $payment->updated_at }}</p></body></html>
