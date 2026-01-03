@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">الإشعارات</h1>
        <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                تحديد الكل كمقروء
            </button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                @if(!$notification->is_read)
                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full ml-2"></span>
                                @endif
                                <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            @if($notification->link)
                                <a href="{{ $notification->link }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-900">
                                    عرض التفاصيل
                                </a>
                            @endif
                        </div>
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                    تحديد كمقروء
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500">لا توجد إشعارات</p>
                </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

