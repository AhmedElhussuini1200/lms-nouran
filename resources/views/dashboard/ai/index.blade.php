@extends('dashboard.partials.master')
@section('content')
<div class="card">
<div class="card-header"><h3 class="card-title fw-bold"><i class="ki-outline ki-message-text fs-2 me-2"></i>{{ __('المساعد الذكي') }}</h3></div>
<div class="card-body">
<form method="POST" action="{{ route('admin.ai.ask') }}">@csrf
<div class="input-group"><input name="question" class="form-control" placeholder="{{ __('اسأل من منهجك...') }}" value="{{ $question ?? '' }}" required />
<button class="btn btn-primary"><i class="ki-outline ki-send fs-4"></i> {{ __('اسأل') }}</button></div>
</form>
@if(isset($result))
<div class="notice d-flex bg-light rounded p-5 mt-5"><div class="fs-6 text-gray-800" style="white-space:pre-line">{{ $result['answer'] }}</div></div>
@foreach(($result['courses'] ?? []) as $c)
<a href="{{ route('admin.courses.show', $c->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light"><span class="symbol symbol-35px"><span class="symbol-label bg-light-primary"><i class="ki-outline ki-book-open fs-4 text-primary"></i></span></span><span class="fw-bold text-gray-800">{{ $c->title }}</span></a>
@endforeach
@foreach(($result['videos'] ?? []) as $v)
<a href="{{ route('admin.videos.show', $v->id) }}" class="d-flex align-items-center gap-3 p-3 rounded text-hover-primary bg-hover-light"><span class="symbol symbol-35px"><span class="symbol-label bg-light-danger"><i class="ki-outline ki-video fs-4 text-danger"></i></span></span><span class="fw-bold text-gray-800">{{ $v->title }}</span></a>
@endforeach
@endif
</div>
</div>
@endsection
