@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-chart fs-2 me-2"></i>{{ __('مركز التقارير') }}</h3></div>
<div class="card-body">
<div class="row g-6">
<div class="col-md-3"><a class="btn btn-light-primary w-100 py-5" href="{{ route('admin.reports.grades') }}"><i class="ki-outline ki-document fs-2x d-block mb-2"></i>{{ __('الدرجات Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-info w-100 py-5" href="{{ route('admin.reports.attendance') }}"><i class="ki-outline ki-calendar fs-2x d-block mb-2"></i>{{ __('الحضور Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-success w-100 py-5" href="{{ route('admin.reports.payments') }}"><i class="ki-outline ki-wallet fs-2x d-block mb-2"></i>{{ __('المدفوعات Excel') }}</a></div>
<div class="col-md-3"><a class="btn btn-light-warning w-100 py-5" href="{{ route('admin.reports.engagement') }}"><i class="ki-outline ki-chart fs-2x d-block mb-2"></i>{{ __('التفاعل PDF') }}</a></div>
</div>
</div>
</div>
@endsection
