@extends('dashboard.partials.master')

@section('content')
<div class="card mb-7">
    <div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
        <div class="flex-grow-1">
            <h2 class="fw-bold mb-2">{{ __('الفيديوهات التعليمية') }}</h2>
            <p class="text-muted mb-0">{{ __('شاهد الدروس المصورة حسب صفك الدراسي') }}</p>
        </div>
        <div class="mt-4 mt-md-0 d-flex gap-3">
            <form method="GET" action="{{ route('admin.videos.index') }}" class="d-flex gap-2">
                @if(in_array(auth('admin')->user()->type,['admin','teacher']))<select data-placeholder="{{ __('الصف الدراسي') }}" data-control="select2" name="grade" class="form-select w-auto" onchange="this.form.submit()">
                    @foreach($grades as $key => $label)
                        <option value="{{ $key }}" {{ request('grade') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>@else<span class="badge badge-light-info fs-7">{{ __('صفك') }}: {{ gradeLockLabel() }}</span>@endif
                @if(auth('admin')->user()->type==='admin' && isset($teachers))<select data-placeholder="{{ __('مدرس') }}" data-control="select2" name="teacher" class="form-select w-auto" onchange="this.form.submit()"><option value="all">{{ __('كل المدرسين') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}" {{ request('teacher')==$t->id?'selected':'' }}>{{ $t->name }}{{ $t->subject ? ' - '.$t->subject : '' }}</option>@endforeach</select>@endif
            </form>
            @if(in_array(auth('admin')->user()->type, ['admin', 'teacher']))
                <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">
                    <i class="ki-outline ki-plus fs-2"></i>
                    <span class="ms-2">{{ __('إضافة فيديو') }}</span>
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row g-6 g-xl-9">
    @forelse($videos as $video)
        <div class="col-sm-6 col-xl-4">
            <div class="card card-flush h-100">
                <div class="card-body p-0">
                    <a href="{{ route('admin.videos.show', $video->id) }}" class="d-block bgi-no-repeat bgi-size-cover bgi-position-center rounded-top" style="height: 200px; background-image: url('{{ $video->thumbnail_url && str_starts_with($video->thumbnail_url, 'http') ? $video->thumbnail_url : asset($video->thumbnail_url ?? 'placeholder_images/default.svg') }}'); background-color: #1e1e2d;">
                        <span class="position-relative d-flex align-items-center justify-content-center h-100">
                            <span class="symbol symbol-60px symbol-circle">
                                <span class="symbol-label bg-white bg-opacity-75">
                                    <i class="ki-outline ki-play fs-2x text-primary"></i>
                                </span>
                            </span>
                        </span>
                    </a>
                    <div class="p-6">
                        <a href="{{ route('admin.videos.show', $video->id) }}" class="fs-5 fw-bold text-gray-900 text-hover-primary d-block mb-2">{{ $video->title }}</a>
                        <p class="text-muted fs-7 mb-4">{{ \Illuminate\Support\Str::limit($video->description, 90) }}</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge badge-light-info">{{ __($video->grade) }}</span>
                            @if(!youtubeId($video->video_url))<span class="badge badge-light-danger" title="{{ __('رابط الفيديو غير صالح') }}"><i class="ki-outline ki-information-5 fs-5"></i></span>@endif
                            <span class="text-muted fs-8">
                                <i class="ki-outline ki-eye fs-7 me-1"></i>{{ $video->views_count }}
                                @if($video->duration_seconds)
                                    • {{ gmdate('H:i:s', $video->duration_seconds) }}
                                @endif
                            </span>
                        </div>
                        <div class="d-flex align-items-center mt-4 pt-4 border-top">
                            <span class="text-muted fs-8">{{ $video->teacher->name ?? '' }} • {{ $video->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card"><div class="card-body text-center text-muted py-10">{{ __('لا توجد فيديوهات') }}@if(in_array(auth('admin')->user()->type,['student','parent'])) — {{ __('لسه مفيش فيديوهات لصفك، تابع مع المدرس') }}@endif</div></div>
        </div>
    @endforelse
</div>

@if(method_exists($videos, 'links'))
<div class="mt-7 d-flex justify-content-center">
    {{ $videos->appends(request()->query())->links() }}
</div>
@endif
@endsection
