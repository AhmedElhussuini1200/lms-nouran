<title>{{ __('Mission') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta charset="utf-8" />
<link rel="shortcut icon" href="{{ isDarkMode() ? asset('favicon.ico') : asset('favicon.ico') }}" />
<!--begin::Fonts-->
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
