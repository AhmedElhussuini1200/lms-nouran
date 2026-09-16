@extends('dashboard.partials.master')

@section('content')
<div class="d-flex align-items-center gap-3 mb-7">
    <a href="{{ route('admin.videos.index') }}" class="btn btn-light btn-sm">
        <i class="ki-outline ki-arrow-right fs-2"></i> {{ __('الفيديوهات') }}
    </a>
    <h2 class="fw-bold mb-0">{{ $video->title }}</h2>
</div>

<div class="row g-6 g-xl-9">
    <div class="col-xl-8">
        <div class="card card-flush">
            <div class="card-body p-0">
                <div class="rounded-top overflow-hidden bg-dark">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $video->video_url }}" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" oncontextmenu="return false"></iframe>
                    </div>
                </div>
                <div class="p-7">
                    <p class="text-gray-700 fs-6">{{ $video->description }}</p>
                    <div class="d-flex flex-wrap gap-5 mt-5 pt-5 border-top text-muted fs-7">
                        <span><i class="ki-outline ki-book-open me-1"></i>{{ __($video->grade) }}</span>
                        <span><i class="ki-outline ki-eye me-1"></i>{{ $video->views_count }} {{ __('مشاهدة') }}</span>
                        <span><i class="ki-outline ki-profile-circle me-1"></i>{{ $video->teacher->name ?? '' }}</span>
                        @if($video->duration_seconds)
                            <span><i class="ki-outline ki-time me-1"></i>{{ gmdate('H:i:s', $video->duration_seconds) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        @if(in_array(auth('admin')->user()->type, ['admin', 'teacher']))
            <div class="card mb-6">
                <div class="card-body d-flex gap-3">
                    <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-light-primary flex-fill">
                        <i class="ki-outline ki-pencil fs-2"></i> {{ __('تعديل') }}
                    </a>
                    <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" class="ajax-form flex-fill" data-success-callback="onAjaxSuccess" >
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('{{ __('هل أنت متأكد؟') }}')" type="submit" class="btn btn-light-danger w-100">
                            <i class="ki-outline ki-trash fs-2"></i> {{ __('حذف') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-header"><h3 class="card-title fw-bold">{{ __('فيديوهات مشابهة') }}</h3></div>
            <div class="card-body py-5">
                @forelse($related as $rel)
                    <a href="{{ route('admin.videos.show', $rel->id) }}" class="d-flex gap-4 mb-5 text-hover-primary">
                        <span class="symbol symbol-80px flex-shrink-0">
                            <span class="symbol-label fs-2 fw-bold bg-light-primary text-primary">{{ mb_substr($rel->title, 0, 1) }}</span>
                        </span>
                        <span>
                            <span class="d-block fw-bold text-gray-900">{{ \Illuminate\Support\Str::limit($rel->title, 50) }}</span>
                            <span class="d-block text-muted fs-8 mt-1">{{ $rel->views_count }} {{ __('مشاهدة') }}</span>
                        </span>
                    </a>
                @empty
                    <p class="text-muted mb-0">{{ __('لا توجد فيديوهات مشابهة') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
