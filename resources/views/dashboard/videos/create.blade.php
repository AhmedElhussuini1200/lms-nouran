@extends('dashboard.partials.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title fw-bold">{{ __('إضافة فيديو جديد') }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('admin.videos.index') }}" class="btn btn-light">{{ __('رجوع') }}</a>
        </div>
    </div>
    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-success-callback="onAjaxSuccess">
        @csrf
        <div class="card-body">
            {!! alertUploadFileHtml() !!}
            <div class="row g-6">
                <div class="col-md-8">
                    <label class="form-label required">{{ __('عنوان الفيديو') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="مثال: شرح الدرس الأول فيزياء" />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">{{ __('الصف الدراسي') }}</label>
                    <select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>
                        @foreach($grades as $key => $label)
                            <option value="{{ $key }}" {{ old('grade') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('المادة') }}</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="فيزياء" />
                </div>
                <div class="col-12">
                    <label class="form-label required">{{ __('رابط الفيديو (يوتيوب)') }}</label>
                    <input type="url" name="video_url" class="form-control" dir="ltr" value="{{ old('video_url') }}" required placeholder="https://www.youtube.com/watch?v=..." />
                    <div class="form-text">{{ __('الصق رابط المشاهدة العادي وسيتحول تلقائياً لرابط embed') }}</div>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('الوصف') }}</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="{{ __('وصف مختصر للفيديو') }}">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('رابط صورة مصغرة (اختياري)') }}</label>
                    <input type="url" name="thumbnail_url" class="form-control" dir="ltr" value="{{ old('thumbnail_url') }}" placeholder="https://..." />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('أو ارفع صورة') }}</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('المدة بالثواني (اختياري)') }}</label>
                    <input type="number" name="duration_seconds" class="form-control" min="0" value="{{ old('duration_seconds') }}" />
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-3">
            <a href="{{ route('admin.videos.index') }}" class="btn btn-light">{{ __('إلغاء') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="ki-outline ki-check fs-2"></i>
                <span class="ms-2">{{ __('حفظ الفيديو') }}</span>
            </button>
        </div>
    </form>
</div>
@endsection
