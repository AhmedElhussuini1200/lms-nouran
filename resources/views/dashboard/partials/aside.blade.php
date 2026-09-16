<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column flex-shrink-0" data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Wrapper-->
    <div class="hover-scroll-overlay-y me-lg-4 mb-5" data-kt-sticky="true" data-kt-sticky-name="app-sidebar-menu-sticky"
        data-kt-sticky-offset="{default: false, xl: '400px'}" data-kt-sticky-release="#kt_app_stats"
        data-kt-sticky-width="auto" data-kt-sticky-left="auto" data-kt-sticky-top="100px"
        data-kt-sticky-animation="false" data-kt-sticky-zindex="95" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_header, #kt_app_footer" data-kt-scroll-wrappers="#kt_app_sidebar_nav"
        data-kt-scroll-offset="0px">
        <!--begin::Nav-->

        <ul class="nav flex-column w-lg-100" id="kt_app_sidebar_nav">
            <!--begin::Nav item - Home-->
            @can('view_dashboard')
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 " data-bs-toggle="tab"
                    href="/dashboard" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Home') }}"
                    aria-selected="true" role="tab">
                    <i class="ki-outline ki-home-2 fs-2"></i>
                </a>
            </li>
            @endcan
            <!--end::Nav item-->

            <!--begin::Nav item - Videos-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.videos.*') }}"
                    href="{{ route('admin.videos.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الفيديوهات') }}">
                    <i class="ki-outline ki-video fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Courses-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.courses.*') }}"
                    href="{{ route('admin.courses.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الحصص') }}">
                    <i class="ki-outline ki-book-open fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Assignments-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute(['admin.assignments.*','admin.submissions.*']) }}"
                    href="{{ route('admin.assignments.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الواجبات') }}">
                    <i class="ki-outline ki-file fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Exams-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute(['admin.exams.*','admin.results.*']) }}"
                    href="{{ route('admin.exams.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الامتحانات') }}">
                    <i class="ki-outline ki-clipboard fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Payments-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.payments.*') }}"
                    href="{{ route('admin.payments.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الحسابات') }}">
                    <i class="ki-outline ki-wallet fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Statuses-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.statuses.*') }}"
                    href="{{ route('admin.statuses.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('جدول الحالات') }}">
                    <i class="ki-outline ki-flag fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - Roles-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.roles.*') }}"
                    href="{{ route('admin.roles.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الأدوار والصلاحيات') }}">
                    <i class="ki-outline ki-shield-tick fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item - WhatsApp-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.whatsapp.*') }}"
                    href="{{ route('admin.whatsapp.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('واتساب') }}">
                    <i class="ki-outline ki-whatsapp fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            @can('view_settings')
            <!--begin::Nav item - Branding-->
            <li class="nav-item mb-2">
                <a class="nav-link btn btn-icon btn-active-info btn-color-gray-600 {{ isActiveRoute('admin.branding.*') }}"
                    href="{{ route('admin.branding.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('الهوية البصرية') }}">
                    <i class="ki-outline ki-paintbucket fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            @endcan



        </ul>



        <!--end::Tabs-->
    </div>
    <!--end::Nav-->
</div>
<!--end::Sidebar-->
