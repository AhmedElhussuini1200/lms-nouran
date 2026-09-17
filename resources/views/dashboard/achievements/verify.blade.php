@extends('dashboard.partials.master')
@section('content')
<div class="card"><div class="card-body text-center py-10">
<h2>✅ {{ __('شهادة موثقة') }}</h2><p>{{ $cert->student->name }} — {{ $cert->exam->title ?? '' }} — {{ $cert->score }}</p>
<code>{{ $cert->code }}</code></div></div>
@endsection
