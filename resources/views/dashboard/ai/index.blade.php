@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-header"><h3 class="card-title">🤖 {{ __('المساعد الذكي') }}</h3></div>
<div class="card-body">
<form method="POST" action="{{ route('admin.ai.ask') }}">@csrf
<div class="input-group"><input name="question" class="form-control" placeholder="{{ __('اسأل من منهجك...') }}" value="{{ $question ?? '' }}" required>
<button class="btn btn-primary">{{ __('اسأل') }}</button></div></form>
@if(isset($result))<div class="alert alert-light mt-4" style="white-space:pre-line">{{ $result['answer'] }}</div>
@foreach(($result['courses'] ?? []) as $c)<div>📚 {{ $c->title }}</div>@endforeach
@foreach(($result['videos'] ?? []) as $v)<div>🎬 {{ $v->title }}</div>@endforeach@endif
</div></div>
@endsection
