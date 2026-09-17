@extends('dashboard.partials.master')
@section('content')
<div class="row g-6">
<div class="col-xl-4"><div class="card"><div class="card-header"><h3 class="card-title">➕ {{ __('سؤال للبنك') }}</h3></div>
<form method="POST" action="{{ route('admin.bank.store') }}">@csrf
<div class="card-body row g-3">
<div class="col-6"><label class="form-label">{{ __('الصف') }}</label><select name="grade" class="form-select" required><option value="1_secondary">{{ __('الأول الثانوي') }}</option><option value="2_secondary">{{ __('الثاني الثانوي') }}</option><option value="3_secondary">{{ __('الثالث الثانوي') }}</option></select></div>
<div class="col-6"><label class="form-label">{{ __('النوع') }}</label><select name="type" class="form-select"><option value="mcq">MCQ</option><option value="true_false">{{ __('صح/خطأ') }}</option><option value="essay">{{ __('مقالي') }}</option></select></div>
<div class="col-6"><label class="form-label">{{ __('الصعوبة') }}</label><select name="difficulty" class="form-select"><option value="easy">{{ __('سهل') }}</option><option value="medium" selected>{{ __('متوسط') }}</option><option value="hard">{{ __('صعب') }}</option></select></div>
<div class="col-6"><label class="form-label">{{ __('الموضوع') }}</label><input name="topic" class="form-control" placeholder="{{ __('مثال: الحركة') }}" /></div>
<div class="col-12"><label class="form-label">{{ __('السؤال') }}</label><textarea name="question" class="form-control" rows="2" required></textarea></div>
<div class="col-12"><label class="form-label">{{ __('الإجابة الصحيحة') }}</label><input name="correct_answer" class="form-control" /></div>
</div><div class="card-footer d-flex justify-content-end"><button class="btn btn-primary">{{ __('حفظ') }}</button></div></form></div></div>
<div class="col-xl-8"><div class="card"><div class="card-header"><h3 class="card-title">🏦 {{ __('بنك الأسئلة') }}</h3></div>
<div class="card-body"><table class="table"><thead><tr><th>{{ __('السؤال') }}</th><th>{{ __('الصعوبة') }}</th><th>{{ __('الموضوع') }}</th></tr></thead>
<tbody>@foreach($items as $it)<tr><td>{{ \Str::limit($it->question,60) }}</td><td>{{ __($it->difficulty) }}</td><td>{{ $it->topic }}</td></tr>@endforeach</tbody></table>
{{ $items->links() }}</div></div></div></div>
@endsection
