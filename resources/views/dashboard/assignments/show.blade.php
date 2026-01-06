@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('assignments.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">← العودة للواجبات</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $assignment->title }}</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="mb-4">
            <p class="text-gray-600">{{ $assignment->description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <span class="text-gray-500">الصف:</span>
                <span class="text-gray-900 font-medium">{{ $assignment->grade }}</span>
            </div>
            <div>
                <span class="text-gray-500">تاريخ الاستحقاق:</span>
                <span class="text-gray-900 font-medium">{{ $assignment->due_date->format('Y-m-d H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">الدرجة الكلية:</span>
                <span class="text-gray-900 font-medium">{{ $assignment->total_marks }}</span>
            </div>
            <div>
                <span class="text-gray-500">المعلم:</span>
                <span class="text-gray-900 font-medium">{{ $assignment->teacher->name }}</span>
            </div>
        </div>
        @if($assignment->file_path)
            <div>
                <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                    تحميل الملف المرفق
                </a>
            </div>
        @endif
    </div>

    @if(auth()->user()->isStudent())
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">تسليم الواجب</h2>
            @if($submission)
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-green-800 font-medium">تم تسليم الواجب</p>
                    <p class="text-sm text-green-600 mt-2">تاريخ التسليم: {{ $submission->submitted_at->format('Y-m-d H:i') }}</p>
                    @if($submission->marks !== null)
                        <p class="text-sm text-green-600 mt-1">الدرجة: {{ $submission->marks }} / {{ $assignment->total_marks }}</p>
                    @endif
                    @if($submission->teacher_feedback)
                        <p class="text-sm text-green-600 mt-2">ملاحظات المعلم: {{ $submission->teacher_feedback }}</p>
                    @endif
                </div>
            @else
                <p class="text-gray-600 mb-4">لم يتم تسليم الواجب بعد</p>
                <form method="POST" action="{{ route('assignments.submit', $assignment) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="submission_text" class="block text-sm font-medium text-gray-700 mb-2">الإجابة</label>
                        <textarea id="submission_text" name="submission_text" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">رفع ملف (اختياري)</label>
                        <input type="file" id="file" name="file" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                        تسليم الواجب
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection

