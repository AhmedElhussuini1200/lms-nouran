<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8" />
<style>
body { font-family: sans-serif; direction: rtl; }
h2 { text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 16px; }
th, td { border: 1px solid #999; padding: 6px; font-size: 12px; text-align: center; }
th { background: #eee; }
.summary { margin-top: 12px; font-size: 13px; }
</style>
</head>
<body>
<h2>{{ __('تقرير المدفوعات الشهري') }} — {{ $month }}</h2>
<table>
<thead><tr><th>{{ __('الطالب') }}</th><th>{{ __('الصف') }}</th><th>{{ __('المطلوب') }}</th><th>{{ __('المدفوع') }}</th><th>{{ __('المتبقي') }}</th><th>{{ __('الحالة') }}</th><th>{{ __('الطريقة') }}</th></tr></thead>
<tbody>
@foreach($payments as $p)
<tr>
<td>{{ $p->student->name ?? '' }}</td>
<td>{{ $p->student->grade ?? '' }}</td>
<td>{{ $p->amount }}</td>
<td>{{ $p->paid_amount }}</td>
<td>{{ $p->amount - $p->paid_amount }}</td>
<td>{{ $p->status->name_ar ?? '' }}</td>
<td>{{ $p->method ?? '' }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="summary">{{ __('الإجمالي المطلوب') }}: {{ $payments->sum('amount') }} — {{ __('المحصل') }}: {{ $payments->sum('paid_amount') }} — {{ __('المتبقي') }}: {{ $payments->sum('amount') - $payments->sum('paid_amount') }}</div>
</body>
</html>
