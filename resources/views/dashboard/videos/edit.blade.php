@extends('dashboard.partials.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title fw-bold">{{ __('تعديل الفيديو') }}: {{ $video->title }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('admin.videos.show', $video->id) }}" class="btn btn-light">{{ __('رجوع') }}</a>
        </div>
    </div>
    <form action="{{ route('admin.videos.update', $video->id) }}" method="POST" enctype="multipart/form-data" class="ajax-form" data-success-callback="onAjaxSuccess">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-6">
                <div class="col-md-8">
                    <label class="form-label required">{{ __('عنوان الفيديو') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $video->title) }}" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">{{ __('الصف الدراسي') }}</label>
                    <select name="grade" data-control="select2" data-placeholder="الصف الدراسي" class="form-select" required>
                        @foreach($grades as $key => $label)
                            <option value="{{ $key }}" {{ old('grade', $video->grade) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('المادة') }}</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject',$video->subject) }}" placeholder="فيزياء" />
                </div>
                <div class="col-12">
                    <label class="form-label required">{{ __('رابط الفيديو') }}</label>
                    <input type="url" name="video_url" class="form-control" dir="ltr" value="{{ old('video_url', $video->video_url) }}" required />
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('الوصف') }}</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $video->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('رابط صورة مصغرة') }}</label>
                    <input type="url" name="thumbnail_url" class="form-control" dir="ltr" value="{{ old('thumbnail_url', $video->thumbnail_url) }}" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('أو ارفع صورة جديدة') }}</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('المدة بالثواني') }}</label>
                    <input type="number" name="duration_seconds" class="form-control" min="0" value="{{ old('duration_seconds', $video->duration_seconds) }}" />
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-3">
            <a href="{{ route('admin.videos.show', $video->id) }}" class="btn btn-light">{{ __('إلغاء') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('حفظ التعديلات') }}</button>
        </div>
    </form>
</div>
@endsection
