<title>{{ brand('site_name', __('Mission')) }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-primary" content="{{ brand('primary_color', '#1b84ff') }}">
<meta name="theme-secondary" content="{{ brand('secondary_color', '#17c653') }}">
<style>
    :root { --brand-primary: {{ brand('primary_color', '#1b84ff') }}; --brand-secondary: {{ brand('secondary_color', '#17c653') }}; }
    .btn-primary, .badge-primary { background-color: var(--brand-primary) !important; border-color: var(--brand-primary) !important; }
    .text-primary { color: var(--brand-primary) !important; }
    .btn-success { background-color: var(--brand-secondary) !important; border-color: var(--brand-secondary) !important; }
</style>
<meta charset="utf-8" />
<link rel="shortcut icon" href="{{ asset(brand('logo')) }}" />
<link rel="apple-touch-icon" href="{{ asset(brand('logo')) }}" />
<!--begin::Fonts-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
<!--begin::Fonts(mandatory for all pages)-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
<!--end::Fonts-->
<!--end::Fonts-->
<!--begin::Vendor Stylesheets(used for this page only)-->
<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Vendor Stylesheets-->
<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
@if (isArabic())
    <link href="{{ asset('assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
@else
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
@endif
<!--end::Global Stylesheets Bundle-->



<style>
    * :not(i) {
        font-family: "Cairo", Helvetica, "sans-serif" !important;
    }

    /* إصلاح مكان علامة التحقق في RTL حتى لا تتداخل مع الكلام */
    [dir="rtl"] .form-control.is-valid,
    [dir="rtl"] .form-select.is-valid {
        background-position: left calc(0.375em + 0.1875rem) center !important;
        padding-left: calc(1.5em + 0.75rem) !important;
        padding-right: 0.75rem !important;
    }
    [dir="rtl"] .form-control.is-invalid,
    [dir="rtl"] .form-select.is-invalid {
        background-position: left calc(0.375em + 0.1875rem) center !important;
        padding-left: calc(1.5em + 0.75rem) !important;
        padding-right: 0.75rem !important;
    }
    /* منع تحديد نص الفيديو والسحب */
    .no-download, .no-download iframe {
        -webkit-user-select: none;
        user-select: none;
    }

    input[type=number].no-arrow {
        -moz-appearance: textfield;
    }

    input[type=number].no-arrow::-webkit-outer-spin-button,
    input[type=number].no-arrow::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<style>
    #loading-div {
        pointer-events: none;

        position: fixed;
        inset: 0;
        /* اختصار left/top/right/bottom:0 */
        width: 100%;
        height: 100%;
        z-index: 99999;

        background-color: #ffffff;
        /* الخلفية اللي هتغطي كل الشاشة */
        display: flex;
        justify-content: center;
        align-items: center;

        /* الصورة في المنتصف */
    }

    #loading-div img {
        width: 70px;
        /* حجم أصغر */
        animation: rotateLoader 1.5s linear infinite;
    }

    /* حركة الدوران */
    @keyframes rotateLoader {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<script>
    // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
</script>
