<!--begin::Header-->
<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: false, lg: true}"
    data-kt-sticky-name="app-header-sticky" data-kt-sticky-offset="{default: false, lg: '300px'}">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
        id="kt_app_header_container">
        <!--begin::Header primary-->
        <div class="d-flex align-items-center justify-content-between flex-row-fluid" id="kt_app_header_wrapper">
            <!--begin::Header logo-->
            <div class="app-header-logo d-flex align-items-center">
                <!--begin::Logo image-->
                <a href="{{ route('admin.index') }}" class="me-5 me-lg-9 d-flex align-items-center gap-3">
                    <img alt="{{ brand('site_name') }}"
                        src="{{ brand('logo') ? asset(brand('logo')) : (isDarkMode() ? asset('placeholder_images/logo-dark-mode.svg') : asset('loges/icon-192.png')) }}"
                        class="h-50px rounded" />
                    <span class="fw-bold fs-5 d-none d-md-inline">{{ brand('site_name') }}</span>
                </a>
                <!--end::Logo image-->

            </div>
            <!--end::Header logo-->
            <!--begin::Menu wrapper-->
            <div class="d-flex align-items-stretch" id="kt_app_header_menu_wrapper">
                <!--begin::Menu holder-->
                <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
                    data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
                    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_header_menu_toggle"
                    data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_menu_wrapper'}">
                    <!--begin::Menu-->
                    <div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-600 menu-state-gray-900 menu-arrow-gray-500 fw-semibold fw-semibold fs-6 align-items-stretch my-5 my-lg-0 px-2 px-lg-0"
                        id="#kt_app_header_menu" data-kt-menu="true">
                        @can('view_dashboard')
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                data-kt-menu-placement="bottom-start" data-kt-menu-offset="-400,0"
                                class="menu-item menu-lg-down-accordion me-0  {{ isTabHere('admin.index') }}">
                                <!--begin:Menu link-->
                                <a class="menu-link" href="{{ route('admin.index') }}">
                                    <span class="menu-title"
                                        style="{{ isTabBold('admin.index') }}">{{ __('Dashboard') }}</span>
                                    <span class="menu-arrow d-lg-none"></span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        @endcan


                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Menu holder-->
            </div>
            <!--end::Menu wrapper-->
            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0">
                <!--begin::User menu-->
                <div class="app-navbar-item me-3" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon btn-icon-gray-600 border border-dashed border-gray-300 w-35px h-35px w-md-40px h-md-40px"
                        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <i class="ki-outline ki-user fs-3"></i>
                    </div>
                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                        data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="d-flex flex-center cursor-pointer symbol symbol-circle symbol-40px">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <div class="symbol-label fs-2 fw-semibold text-primary">
                                            {{ substr(auth()->user()->name, 0, 2) }}


                                        </div>

                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Username-->
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5 text-white">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <a href="#"
                                        class="fw-semibold text-muted text-hover-primary fs-7 text-white">{{ auth()->user()->email }}</a>
                                </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a href="{{ route('admin.admins.profile-info') }}"
                                class="menu-link px-5">{{ __('Profile') }}</a>
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">{{ __('Mode') }}
                                    <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                        <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                                        <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                                    </span></span>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                                data-kt-menu="true" data-kt-element="theme-mode-menu">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="{{ route('admin.admins.change-mode', 'light') }}"
                                        class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-outline ki-night-day fs-2"></i>
                                        </span>
                                        <span class="menu-title">{{ __('Light') }}</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                        data-kt-value="dark">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-outline ki-moon fs-2"></i>
                                        </span>
                                        <span class="menu-title">{{ __('Dark') }}</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                        data-kt-value="system">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-outline ki-screen fs-2"></i>
                                        </span>
                                        <span class="menu-title">{{ __('System') }}</span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="right-end" data-kt-menu-offset="-15px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">
                                    {{ __('Language') }}
                                    @if (isArabic())
                                        <span
                                            class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                            {{ __('Arabic') }}
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="{{ asset('assets/media/flags/saudi-arabia.svg') }}" alt="" />
                                        </span>
                                    @else
                                        <span
                                            class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                            {{ __('English') }}
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                                        </span>
                                    @endif
                                </span>
                            </a>
                            <!--begin::Menu sub-->
                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="{{ route('admin.change-language', 'en') }}"
                                        class="menu-link d-flex px-5 @if (!isArabic()) active @endif">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1"
                                                src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                                        </span>
                                        {{ __('English') }}
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="{{ route('admin.change-language', 'ar') }}"
                                        class="menu-link d-flex px-5 @if (isArabic()) active @endif">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1"
                                                src="{{ asset('assets/media/flags/saudi-arabia.svg') }}" alt="" />
                                        </span>
                                        {{ __('Arabic') }}
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu sub-->
                        </div>
                        <!--end::Menu item-->
                        <!--end::Menu item-->
                        <form class="logout-form" method="post" action="{{ route('admin.logout') }}">
                            @csrf
                        </form>
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a href="javascript:" onclick="$('.logout-form').submit()"
                                class="menu-link px-5">{{ __('Logout') }}</a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::User account menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User menu-->
                <!--begin::Notifications-->
                <div class="app-navbar-item me-3">
                    <!--begin::Menu- wrapper-->
                    <div class="btn btn-icon btn-icon-gray-600 border border-dashed border-gray-300 w-35px h-35px w-md-40px h-md-40px position-relative"
                        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <!--begin::Bullet-->
                        <span
                            class="bullet bullet-dot bg-danger h-6px w-6px position-absolute animation-blink translate-middle top-0 start-50 {{ $unreadNotifications->count() == 0 ? 'd-none' : '' }}"></span>
                        <!--end::Bullet-->
                        <i class="ki-outline ki-notification fs-3">
                        </i>
                    </div>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true"
                        id="kt_menu_notifications">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-color: #016946;">
                            <div class="d-flex justify-content-between align-items-center">

                                <!--begin::Title-->
                                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                                    {{ __('Notifications') }}
                                    @if ($unreadNotifications->count() > 0)
                                        <span
                                            class="fs-8 opacity-75 ps-3 notifications-counter">{{ $unreadNotifications->count() . __('unread') }}</span>
                                    @else
                                        <span class="fs-8 opacity-75 ps-3 notifications-counter">
                                            {{ __('nothing new') }}</span>
                                    @endif
                                </h3>
                                <!--end::Title-->
                                <a href="{{ route('admin.notifications.mark_all_as_read') }}"
                                    class="text-white fw-semibold px-9 mt-10 mb-6">{{ __('Mark all as read') }}</a>
                            </div>
                            <!--begin::Tabs-->
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                                        data-bs-toggle="tab" href="#all_notifications" aria-selected="false"
                                        tabindex="-1" role="tab">{{ __('All') }}</a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active"
                                        data-bs-toggle="tab" href="#unread_notifications" aria-selected="true"
                                        role="tab">{{ __('Unread') }}</a>
                                </li>
                            </ul>
                            <!--end::Tabs-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Tab content-->
                        <div class="tab-content">
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade" id="all_notifications" role="tabpanel">
                                <!--begin::Wrapper-->
                                <div class="scroll-y mh-325px min-h-325px my-5 px-8" id="all-notifications-container">
                                    @forelse ($allNotifications->limit(10)->get() as $notification)
                                        <div class="d-flex flex-stack py-4 notification-item">
                                            <!--begin::Section-->
                                            <div class="d-flex align-items-center">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-50px me-4">
                                                    <span
                                                        class="symbol-label bg-light-{{ $notification->data['color'] }}">
                                                        <span
                                                            class="svg-icon svg-icon-2x svg-icon-{{ $notification->data['color'] }}">
                                                            {!! $notification->data['icon'] !!}
                                                        </span>
                                                    </span>
                                                </div>
                                                <!--end::Symbol-->

                                                <!--begin::Title-->
                                                <div class="mb-0 me-2">
                                                    <a href="{{ route('admin.notifications.mark_as_read', $notification->id) }}"
                                                        class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $notification->data['title_' . app()->getLocale()] }}</a>
                                                    <div class="text-gray-400 fs-7">
                                                        {{ $notification->data['description_' . app()->getLocale()] }}
                                                    </div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Section-->

                                            <!--begin::Label-->
                                            <span
                                                class="badge badge-light fs-8">{{ $notification->created_at->diffForHumans() }}</span>
                                            <!--end::Label-->
                                        </div>
                                    @empty
                                        <div class="d-flex flex-column px-9 pb-5 no-notifications-alert">
                                            <!--begin::Illustration-->
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-200px" alt="image"
                                                    src="{{ asset('assets/media/illustrations/unitedpalms-1/notifications.png') }}">
                                            </div>
                                            <!--end::Illustration-->
                                            <!--begin::Section-->
                                            <div class="pt-10 pb-0">
                                                <!--begin::Title-->
                                                <h3 class="text-dark text-center fw-bold">
                                                    {{ __('There are no new notifications!') }}
                                                </h3>
                                                <!--end::Title-->
                                                <!--begin::Text-->
                                                <div class="text-center text-gray-600 fw-semibold pt-1">
                                                    {{ __('Here it shows you all the notifications from the website to be aware of the latest important processes and events that need to be re-reviewed and a new action taken with them.') }}
                                                </div>
                                                <!--end::Text-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                    @endforelse

                                    <!--begin::Item-->
                                    @if ($allNotifications->count() > 10)
                                        <!--begin::Item-->
                                        <button type="submit" class="btn border-none p-0 d-flex m-auto"
                                            data-kt-indicator="" id="all-load-more">
                                            <span class="indicator-label">
                                                <span class="svg-icon svg-icon-danger svg-icon-2x">
                                                    <!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo3\dist/../src/media/svg/icons\Navigation\Angle-double-down.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                            <path
                                                                d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z"
                                                                fill="#000000" fill-rule="nonzero"
                                                                transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) " />
                                                            <path
                                                                d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z"
                                                                fill="#000000" fill-rule="nonzero" opacity="0.3"
                                                                transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) " />
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </span>
                                            <span class="indicator-progress">
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                        <!--end::Item-->
                                    @endif
                                    <!--end::Item-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tab panel-->
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade show active" id="unread_notifications" role="tabpanel">
                                <!--begin::Wrapper-->
                                <div class="scroll-y mh-325px min-h-325px my-5 px-8"
                                    id="unread-notifications-container">
                                    @forelse ($unreadNotifications->limit(10)->get() as $notification)
                                        <div class="d-flex flex-stack py-4 notification-item">
                                            <!--begin::Section-->
                                            <div class="d-flex align-items-center">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-50px me-4">
                                                    <span
                                                        class="symbol-label bg-light-{{ $notification->data['color'] }}">
                                                        <span
                                                            class="svg-icon svg-icon-2x svg-icon-{{ $notification->data['color'] }}">
                                                            {!! $notification->data['icon'] !!}
                                                        </span>
                                                    </span>
                                                </div>
                                                <!--end::Symbol-->

                                                <!--begin::Title-->
                                                <div class="mb-0 me-2">
                                                    <a href="{{ route('admin.notifications.mark_as_read', $notification->id) }}"
                                                        class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $notification->data['title_' . app()->getLocale()] }}</a>
                                                    <div class="text-gray-400 fs-7">
                                                        {{ $notification->data['description_' . app()->getLocale()] }}
                                                    </div>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Section-->

                                            <!--begin::Label-->
                                            <span
                                                class="badge badge-light fs-8">{{ $notification->created_at->diffForHumans() }}</span>
                                            <!--end::Label-->
                                        </div>
                                    @empty
                                        <div class="d-flex flex-column px-9 pb-5" id="no-notification-alert">
                                            <!--begin::Illustration-->
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-200px" alt="image"
                                                    src="{{ asset('assets/media/illustrations/unitedpalms-1/notifications.png') }}">
                                            </div>
                                            <!--end::Illustration-->
                                            <!--begin::Section-->
                                            <div class="pt-10 pb-0">
                                                <!--begin::Title-->
                                                <h3 class="text-dark text-center fw-bold">
                                                    {{ __('There are no new notifications!') }}
                                                </h3>
                                                <!--end::Title-->
                                                <!--begin::Text-->
                                                <div class="text-center text-gray-600 fw-semibold pt-1">
                                                    {{ __('Here it shows you all the notifications from the website to be aware of the latest important processes and events that need to be re-reviewed and a new action taken with them.') }}
                                                </div>
                                                <!--end::Text-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                    @endforelse
                                    <!--begin::Item-->
                                    @if ($unreadNotifications->count() > 10)
                                        <!--begin::Item-->
                                        <button type="submit" class="btn border-none p-0 d-flex m-auto"
                                            data-kt-indicator="" id="unread-load-more">
                                            <span class="indicator-label">
                                                <span class="svg-icon svg-icon-danger svg-icon-2x">
                                                    <!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo3\dist/../src/media/svg/icons\Navigation\Angle-double-down.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                            <path
                                                                d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z"
                                                                fill="#000000" fill-rule="nonzero"
                                                                transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) " />
                                                            <path
                                                                d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z"
                                                                fill="#000000" fill-rule="nonzero" opacity="0.3"
                                                                transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) " />
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </span>
                                            <span class="indicator-progress">
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                        <!--end::Item-->
                                    @endif
                                    <!--end::Item-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Notifications-->
                <!--begin::Settings-->
                @canany(['view_settings', 'view_roles', 'view_commission_tax'])
                    <div class="app-navbar-item me-3">
                        <a
                            href="{{ route('admin.branding.index') }}">
                            <div class="btn btn-icon btn-icon-gray-600 border border-dashed border-gray-300 w-35px h-35px w-md-40px h-md-40px"
                                data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                                data-kt-menu-placement="bottom-end">
                                <i class="ki-outline ki-setting-3 fs-3"></i>
                            </div>
                        </a>
                    </div>
                @endcanany
                <!--end::Settings-->
                <!--begin::Sidebar menu toggle-->
                <div class="app-navbar-item d-flex align-items-center d-lg-none ms-1 me-n3">
                    <a href="#" class="btn btn-icon btn-color-gray-500 btn-active-color-primary w-35px h-35px"
                        id="kt_app_sidebar_mobile_toggle">
                        <i class="ki-outline ki-abstract-14 fs-1"></i>
                    </a>
                </div>
                <!--end::Sidebar menu toggle-->
                <!--begin::Header menu toggle-->
                <div class="app-navbar-item d-flex align-items-center d-lg-none ms-1 me-n3">
                    <a href="#" class="btn btn-icon btn-color-gray-500 btn-active-color-primary w-35px h-35px"
                        id="kt_app_header_menu_toggle">
                        <i class="ki-outline ki-text-align-left fs-1"></i>
                    </a>
                </div>
                <!--end::Header menu toggle-->
            </div>
            <!--end::Navbar-->
        </div>
        <!--end::Header primary-->
    </div>
    <!--end::Header container-->
</div>
<!--end::Header-->
