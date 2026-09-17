<!DOCTYPE html><html dir="rtl"><head><meta charset="utf-8"><style>body{font-family:dejavusans;text-align:center}</style></head>
<body><div style="border:10px double #1b84ff;padding:60px">
<h1>شهادة تفوق 🎓</h1><p>تشهد إدارة المنصة بأن</p><h2>{{ $certificate->student->name ?? '' }}</h2>
<p>قد اجتاز {{ $certificate->exam->title ?? 'الدورة' }} بدرجة {{ $certificate->score }}</p>
<p>كود التحقق: <b>{{ $certificate->code }}</b></p></div></body></html>
