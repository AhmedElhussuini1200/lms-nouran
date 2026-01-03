@extends('layouts.app')

@section('title', $video->title)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('videos.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">← العودة للفيديوهات</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $video->title }}</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                <iframe src="{{ $video->video_url }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
        <div class="mb-4">
            <p class="text-gray-600">{{ $video->description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">الصف:</span>
                <span class="text-gray-900 font-medium">{{ $video->grade }}</span>
            </div>
            <div>
                <span class="text-gray-500">المشاهدات:</span>
                <span class="text-gray-900 font-medium">{{ $video->views_count }}</span>
            </div>
            <div>
                <span class="text-gray-500">المعلم:</span>
                <span class="text-gray-900 font-medium">{{ $video->teacher->name }}</span>
            </div>
            @if($video->duration_seconds)
                <div>
                    <span class="text-gray-500">المدة:</span>
                    <span class="text-gray-900 font-medium">{{ gmdate('H:i:s', $video->duration_seconds) }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

