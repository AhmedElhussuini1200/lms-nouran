<!DOCTYPE html><html dir="rtl"><head><meta charset="utf-8"><style>body{font-family:dejavusans}table{width:100%;border-collapse:collapse}td,th{border:1px solid #ccc;padding:6px}</style></head>
<body><h2>تقرير التفاعل - {{ $grade }}</h2>
<table><thead><tr><th>الطالب</th><th>فيديوهات مكتملة</th><th>النقاط</th><th>متوسط الدرجات</th></tr></thead>
<tbody>@foreach($students as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->videos_completed }}</td><td>{{ $s->points }}</td><td>{{ round($s->avg,1) }}</td></tr>@endforeach</tbody></table></body></html>
