@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title">📊 {{ __('مركز التقارير') }}</h3></div>
<div class="card-body"><div class="row g-4">
<div class="col-md-3"><a class="btn btn-light-primary w-100 py-5" href="{{ route('admin.reports.grades') }}">📝 {{ __('الدرجات Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-info w-100 py-5" href="{{ route('admin.reports.attendance') }}">📅 {{ __('الحضور Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-success w-100 py-5" href="{{ route('admin.reports.payments') }}">💰 {{ __('المدفوعات Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-warning w-100 py-5" href="{{ route('admin.reports.engagement') }}">🔥 {{ __('التفاعل PDF') }}</a></div>
</div></div></div>
@endsection
