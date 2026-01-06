@extends('dashboard.partials.master')

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <!--begin::Welcome card-->
            <div class="card mb-7">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6 px-6">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-2">
                            {{ __('مرحبا،') }} {{ auth('admin')->user()->name }}
                        </h2>
                        <p class="text-muted mb-0">
                            {{ __('هذه لوحة تحكم المدرسة، تقدر من هنا تدير المدرسين، الطلاب، أولياء الأمور والمحتوى التعليمي.') }}
                        </p>
                    </div>
                    <div class="mt-4 mt-md-0">
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary me-2">
                            <i class="ki-outline ki-plus fs-2"></i>
                            <span class="ms-2">{{ __('إضافة مستخدم جديد') }}</span>
                        </a>
                        <a href="{{ route('admin.admins.profile-info') }}" class="btn btn-light-primary">
                            <i class="ki-outline ki-user fs-2"></i>
                            <span class="ms-2">{{ __('الملف الشخصي') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Welcome card-->

            <!--begin::Stats widgets-->
            <div class="row g-6 g-xl-9 mb-7">
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-between py-5 px-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-outline ki-teacher fs-2 text-primary"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fs-6 text-gray-800 fw-bold">{{ __('عدد المدرسين') }}</div>
                                    <div class="fs-2 fw-bolder text-primary">
                                        {{-- هنا تقدر تحط عدد حقيقي مثلاً $stats['teachers'] --}}
                                        0
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.admins.index') }}" class="text-primary fw-semibold">
                                {{ __('عرض المدرسين') }}
                                <i class="ki-outline ki-arrow-left fs-4 ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-between py-5 px-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-outline ki-profile-user fs-2 text-success"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="fs-6 text-gray-800 fw-bold">{{ __('عدد الطلاب') }}</span>
                            <span class="fs-2 fw-bolder text-success">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-between py-5 px-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-outline ki-book-open fs-2 text-warning"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="fs-6 text-gray-800 fw-bold">{{ __('عدد الكورسات / الحصص') }}</span>
                            <span class="fs-2 fw-bolder text-warning">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100">
                        <div class="card-body d-flex flex-column justify-content-between py-5 px-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-danger">
                                        <i class="ki-outline ki-video fs-2 text-danger"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="fs-6 text-gray-800 fw-bold">{{ __('عدد الفيديوهات') }}</span>
                            <span class="fs-2 fw-bolder text-danger">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats widgets-->

            <!--begin::Quick actions-->
            <div class="row g-6 g-xl-9">
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">{{ __('إجراءات سريعة') }}</h3>
                        </div>
                        <div class="card-body py-5">
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-light-primary">
                                    <i class="ki-outline ki-people fs-2 me-2"></i>
                                    {{ __('إدارة المستخدمين') }}
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-light-info">
                                    <i class="ki-outline ki-setting-3 fs-2 me-2"></i>
                                    {{ __('إعدادات النظام') }}
                                </a>
                                <a href="{{ route('admin.notifications.mark_all_as_read') }}"
                                    class="btn btn-light-warning">
                                    <i class="ki-outline ki-notification fs-2 me-2"></i>
                                    {{ __('تصفير الإشعارات') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">{{ __('أحدث النشاطات') }}</h3>
                        </div>
                        <div class="card-body py-5">
                            <p class="text-muted mb-0">
                                {{ __('هنا تقدر تعرض آخر الطلاب المسجلين، آخر الواجبات المضافة، أو أي نشاط مهم في المنصة.') }}
                            </p>
                            {{-- لاحقاً تقدر تستبدل الجزء ده بجدول أو Timeline حقيقي --}}
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Quick actions-->
        </div>
    </div>
@endsection
