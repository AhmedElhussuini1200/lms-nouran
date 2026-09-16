<!--begin::Footer-->
<div id="kt_app_footer" class="app-footer">
    <!--begin::Footer container-->
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        <!--begin::Copyright-->
        <div class="text-dark order-2 order-md-1">
            <span class="text-muted fw-semibold me-1">{{ now()->year }}&copy;</span>
            <span class="text-gray-800 fw-bold">{{ brand('site_name', __('منصة نوران التعليمية')) }}</span>
            <span class="text-muted fw-semibold ms-1">— {{ __('جميع الحقوق محفوظة') }}</span>
        </div>
        <!--end::Copyright-->
        <!--begin::Developed by-->
        <div class="d-flex align-items-center fw-bold fs-6 order-1 order-md-2">
            <a href="https://github.com/Ahmedelhussuini900" target="_blank"
                class="text-muted text-hover-primary px-2">
                {{ __('Developed by') }}
                <img class="mx-2 rounded-circle"
                    src="{{ asset('assets/logo/177956648.jpeg') }}"
                    alt="Ahmed"
                    width="40"
                    height="40">
            </a>
        </div>
        <!--end::Developed by-->
    </div>
    <!--end::Footer container-->
</div>
<!--end::Footer-->
