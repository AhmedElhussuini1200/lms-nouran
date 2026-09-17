<!DOCTYPE html><html dir="rtl"><head><meta charset="utf-8"><style>body{font-family:dejavusans}table{width:100%;border-collapse:collapse}td,th{border:1px solid #ccc;padding:6px}</style></head>
<body><h2>تقرير متابعة — {{ $student->name }}</h2>
<p>الغياب: {{ $absences }} | عدد الامتحانات: {{ $results->count() }}</p>
<table><thead><tr><th>الامتحان</th><th>الدرجة</th><th>التاريخ</th></tr></thead>
<tbody>@foreach($results as $r)<tr><td>{{ $r->exam->title ?? '' }}</td><td>{{ $r->marks_obtained }}</td><td>{{ $r->created_at->format('Y-m-d') }}</td></tr>@endforeach</tbody></table>
<h3>المدفوعات</h3>
<table><thead><tr><th>الشهر</th><th>المطلوب</th><th>المدفوع</th><th>المتبقي</th></tr></thead>
<tbody>@foreach($payments as $p)<tr><td>{{ $p->month }}</td><td>{{ $p->amount }}</td><td>{{ $p->paid_amount }}</td><td>{{ $p->remaining }}</td></tr>@endforeach</tbody></table></body></html>
