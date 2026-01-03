@extends('layouts.app')

@section('title', 'الفيديوهات')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">الفيديوهات</h1>
        @if(auth()->user()->isTeacher())
            <a href="{{ route('videos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                إضافة فيديو جديد
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($videos as $video)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($video->thumbnail_url)
                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <a href="{{ route('videos.show', $video) }}" class="hover:text-indigo-600">
                            {{ $video->title }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($video->description, 100) }}</p>
                    <div class="flex items-center text-xs text-gray-500">
                        <span>المشاهدات: {{ $video->views_count }}</span>
                        @if($video->duration_seconds)
                            <span class="mx-2">•</span>
                            <span>المدة: {{ gmdate('H:i:s', $video->duration_seconds) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center bg-white shadow rounded-lg">
                <p class="text-gray-500">لا توجد فيديوهات</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $videos->links() }}
    </div>
</div>
@endsection

