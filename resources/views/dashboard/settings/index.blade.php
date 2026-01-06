@extends('dashboard.partials.master')
@push('styles')
    <link href="{{ asset('assets/css/datatables' . (isDarkMode() ? '.dark' : '') . '.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/js/custom/datatables/datatables.bundle' . (isArabic() ? '.rtl' : '') . '.css') }}"
        rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin:::Tabs-->
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-transparent fs-4 fw-semibold mb-15">
                    @can('view_settings')
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary d-flex align-items-center pb-5 active" data-bs-toggle="tab"
                                href="#kt_ecommerce_settings_general">
                                <i class="ki-outline ki-home fs-2 me-2"></i>{{ __('General') }}</a>
                        </li>
                        <!--end:::Tab item-->
                    @endcan
                    @can('view_roles')
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary d-flex align-items-center pb-5" data-bs-toggle="tab" t
                                href="#kt_ecommerce_settings_roles">
                                <i class="ki-outline ki-user-tick fs-2 me-2"></i>{{ __('Roles') }}</a>
                        </li>
                        <!--end:::Tab item-->
                    @endcan
                    @can('view_settings')
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary d-flex align-items-center pb-5" data-bs-toggle="tab"
                                href="#kt_ecommerce_settings_terms">
                                <i class="ki-outline ki-shield-tick fs-2 me-2"></i>{{ __('Terms and conditions') }}</a>
                        </li>
                        <!--end:::Tab item-->
                    @endcan
                    @can('view_settings')
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary d-flex align-items-center pb-5" data-bs-toggle="tab"
                                href="#kt_ecommerce_settings_privacy">
                                <i class="ki-outline ki-lock fs-2 me-2"></i>{{ __('Privacy Policy') }}</a>
                        </li>
                        <!--end:::Tab item-->
                    @endcan

                </ul>
                <!--end:::Tabs-->
                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_ecommerce_settings_general" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form " class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'general']) }}" method="post"
                            data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('General Settings') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="row fv-row mb-7">
                                <div class="col-md-2 text-md-end">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-semibold form-label mt-3">
                                        <span class="required">{{ __('Address in Arabic') }}</span>
                                    </label>
                                    <!--end::Label-->
                                </div>
                                <div class="col-md-9">
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-solid" id="address_ar_inp"
                                        name="address_ar" value="{{ setting('address_ar') }}" />
                                    <!--end::Input-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="address_ar"></div>
                                </div>

                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row fv-row mb-7">
                                <div class="col-md-2 text-md-end">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-semibold form-label mt-3">
                                        <span class="required">{{ __('Address in English') }}</span>
                                    </label>
                                    <!--end::Label-->
                                </div>
                                <div class="col-md-9">
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-solid" id="address_en_inp"
                                        name="address_en" value="{{ setting('address_en') }}" />
                                    <!--end::Input-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="address_en"></div>
                                </div>

                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row fv-row mb-7">
                                <div class="col-md-2 text-md-end">

                                    <!--begin::Label-->
                                    <label class="fs-6 fw-semibold form-label mt-3">
                                        <span class="required">{{ __('Email') }}</span>
                                    </label>
                                    <!--end::Label-->
                                </div>
                                <div class="col-md-9">
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-solid" id="email_inp"
                                        name="email" value="{{ setting('email') }}" />
                                    <!--end::Input-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="email"></div>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row fv-row mb-7">
                                <div class="col-md-2 text-md-end">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-semibold form-label mt-3">
                                        <span class="required">{{ __('Phone') }}</span>
                                    </label>
                                    <!--end::Label-->
                                </div>
                                <div class="col-md-9">
                                    <!--begin::Input-->
                                    <input type="tel" class="form-control form-control-solid" id="phone_inp"
                                        name="phone" value="{{ setting('phone') }}"
                                        data-kt-ecommerce-settings-type="tagify" />
                                    <!--end::Input-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="phone"></div>
                                </div>

                            </div>
                            <!--end::Input group-->

                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_settings_roles" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_store" class="form" action="#">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Roles') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Content-->
                            <div class="card-body">
                                <!-- begin :: Row -->
                                <div class="row">
                                    <!-- begin :: Add Role -->
                                    <div class="col-md-4 my-10">
                                        <!--begin::Card-->
                                        <div class="card h-md-100  shadow rounded">
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-center">
                                                <!--begin::Button-->
                                                <button type="button"
                                                    class="btn btn-clear d-flex flex-column flex-center p-0"
                                                    id="add-role-btn" data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_add_role">
                                                    <!--begin::Illustration-->
                                                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}"
                                                        alt="" class="mw-100 mh-400px">
                                                    <!--end::Illustration-->
                                                    <!--begin::Label-->
                                                    <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">
                                                        <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2022-11-29-094551/core/html/src/media/icons/duotune/general/gen041.svg-->
                                                        <span class="svg-icon svg-icon-muted svg-icon-2x"><svg
                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="10" fill="currentColor" />
                                                                <rect x="10.8891" y="17.8033" width="12"
                                                                    height="2" rx="1"
                                                                    transform="rotate(-90 10.8891 17.8033)"
                                                                    fill="currentColor" />
                                                                <rect x="6.01041" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                        {{ __('Add new role') }}
                                                    </div>
                                                    <!--end::Label-->
                                                </button>
                                                <!--begin::Button-->
                                            </div>
                                            <!--begin::Card body-->
                                        </div>
                                        <!--begin::Card-->
                                    </div>
                                    <!-- end   :: Add Role -->
                                    <!-- begin :: Roles -->
                                    @foreach ($roles as $role)
                                        <div class="col-md-4 my-10 ">
                                            <!--begin::Card-->
                                            <div class="card card-flush h-md-100 shadow rounded">
                                                <!--begin::Card header-->
                                                <div class="card-header">
                                                    <!--begin::Card title-->
                                                    <div class="card-title">
                                                        <h2> {{ $role->name }}</h2>
                                                    </div>
                                                    <!--end::Card title-->
                                                </div>
                                                <!--end::Card header-->
                                                <!--begin::Card body-->
                                                <div class="card-body pt-1">
                                                    <!--begin::Users-->
                                                    <div class="fw-bolder text-gray-600 mb-5">
                                                        {{ $role->admins ? __('Number of employees who have this role :') . ' ' . $role->admins->count() : __('Not associated with any employee') }}
                                                    </div>
                                                    <!--end::Users-->
                                                    <!--begin::Permissions-->
                                                    <div class="d-flex flex-column text-gray-600">

                                                        @foreach ($role->abilities->shuffle()->take(5) as $ability)
                                                            <div class="d-flex align-items-center py-2">
                                                                <span class="bullet bg-primary me-3"></span>
                                                                {{ __($ability->action) . ' ' . __(ucfirst(str_replace('_', ' ', $ability->category))) }}
                                                            </div>
                                                        @endforeach

                                                        @if ($role->abilities->count() - 5 > 0)
                                                            <div class="d-flex align-items-center py-2">
                                                                <span class="bullet bg-primary me-3"></span>
                                                                <em>{{ $role->abilities->count() - 5 . ' ' . __('and more ...') }}</em>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <!--end::Permissions-->
                                                </div>
                                                <!--end::Card body-->
                                                <!--begin::Card footer-->
                                                <div class="card-footer text-center flex-wrap pt-0">
                                                    <a id="view-role"
                                                        href="{{ route('dashboard.admin.settings.roles.show', $role->id) }}"
                                                        class="btn btn-light btn-active-primary my-1 me-2">{{ __('Show role') }}</a>

                                                    <button type="button"
                                                        class="btn btn-light btn-active-light-primary my-1 edit-role-btn"
                                                        data-role-id="{{ $role->id }}">

                                                        <span class="indicator-label">{{ __('Edit role') }}</span>

                                                        <!-- begin :: Indicator -->
                                                        <span class="indicator-progress">{{ __('Please wait ...') }}
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                        <!-- end   :: Indicator -->

                                                    </button>

                                                </div>
                                                <!--end::Card footer-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                    @endforeach
                                    <!-- end   :: Roles -->
                                </div>
                                <!-- end   :: Row-->

                            </div>
                            <!--end::Content-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_settings_terms" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form " class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'terms']) }}" method="post"
                            data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Terms and conditions') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col-lg-6">
                                    <!--begin::Label-->
                                    <label class="form-label required">{{ __('Terms and conditions in arabic') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Editor-->
                                    <textarea name="terms_ar" id="terms_ar_inp" data-kt-autosize="true"
                                        placeholder="{{ __('Terms and conditions in arabic') }}" class="tox-target">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ setting('terms_ar') }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </textarea>
                                    <!--end::Editor-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="terms_ar">
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <div class="col-lg-6">
                                    <!--begin::Label-->
                                    <label class="form-label required">{{ __('Terms and conditions in english') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Editor-->
                                    <textarea name="terms_en" id="terms_en_inp" data-kt-autosize="true"
                                        placeholder="{{ __('Terms and conditions in english') }}" class="tox-target">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ setting('terms_en') }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </textarea>
                                    <!--end::Editor-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="terms_en">
                                    </div>
                                    <!--end::Description-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_settings_privacy" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form" class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'privacy']) }}" method="post"
                            data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Privacy Policy') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col-lg-6">
                                    <!--begin::Label-->
                                    <label class="form-label required">{{ __('Privacy policy in arabic') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Editor-->
                                    <textarea name="privacy_policy_ar" id="privacy_policy_ar_inp" data-kt-autosize="true"
                                        placeholder="{{ __('Privacy policy in arabic') }}" class="tox-target">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ setting('privacy_policy_ar') }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </textarea>
                                    <!--end::Editor-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="privacy_policy_ar">
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <div class="col-lg-6">
                                    <!--begin::Label-->
                                    <label class="form-label required">{{ __('Privacy policy in english') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Editor-->
                                    <textarea name="privacy_policy_en" id="privacy_policy_en_inp" data-kt-autosize="true"
                                        placeholder="{{ __('Privacy policy in english') }}" class="tox-target">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ setting('privacy_policy_en') }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </textarea>
                                    <!--end::Editor-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="privacy_policy_en">
                                    </div>
                                    <!--end::Description-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_commission_tax" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form " class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'commission_tax']) }}"
                            method="post" data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Commission and Taxes') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label
                                        class="required form-label">{{ __('Service Requester’s Commission Percentage') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="number" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="owner_commition_percentage_inp" name="owner_commition_percentage"
                                        value="{{ setting('owner_commition_percentage') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="owner_commition_percentage"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label
                                        class="required form-label">{{ __('Service Requester’s Commission Fixed') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow numeric-input"
                                        id="owner_commition_value_inp" name="owner_commition_value"
                                        value="{{ number_format(setting('owner_commition_value'), 0, '.', ',') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="owner_commition_value">
                                    </div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label
                                        class="required form-label">{{ __("Professional's Commission Percent") }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="number" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="vendor_commition_percentage_inp" name="vendor_commition_percentage"
                                        value="{{ setting('vendor_commition_percentage') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="vendor_commition_percentage"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class="required form-label">{{ __("Professional's Commission Fixed") }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow numeric-input"
                                        id="vendor_commition_value_inp" name="vendor_commition_value"
                                        value="{{ number_format(setting('vendor_commition_value'), 0, '.', ',') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="vendor_commition_value">
                                    </div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class="required form-label">{{ __('Tax') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="number" step="0.1" style="direction: {{ getDirection() }};"
                                        class="form-control form-control-solid mb-2 no-arrow" id="tax_inp"
                                        name="tax" value="{{ setting('tax') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="tax"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_penalties" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form " class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'penalties']) }}"
                            method="post" data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Penalty') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label for="penalty_amount_inp"
                                        class="required form-label">{{ __('Penalty Amount') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Select2-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow numeric-input"
                                        id="penalty_amount_inp" name="penalty_amount"
                                        value="{{ number_format(setting('penalty_amount'), 0, '.', ',') }}" />
                                    <div class="fv-plugins-message-container invalid-feedback" id="penalty_amount">
                                    </div>

                                    <!--end::Select2-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class="required form-label">{{ __('Max Cancellations') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text"
                                        class="form-control form-control-solid mb-2 no-arrow numeric-input"
                                        id="max_cancellations_inp" name="max_cancellations"
                                        value="{{ number_format(setting('max_cancellations'), 0, '.', ',') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback" id="max_cancellations">
                                    </div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ecommerce_mobile_app" role="tabpanel">
                        <!--begin::Form-->
                        <form id="kt_ecommerce_settings_general_form " class="form ajax-form"
                            action="{{ route('dashboard.admin.settings.store', ['type' => 'mobile_app']) }}"
                            method="post" data-success-callback="onAjaxSuccess" data-hide-alert="true">
                            <!--begin::Heading-->
                            <div class="row mb-7">
                                <div class="col-md-9">
                                    <h2>{{ __('Mobile App Maintenance') }}</h2>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            {{-- android --}}
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label for="android_exact_blocked_version_inp"
                                        class=" form-label">{{ __('Android Exact Blocked Version') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Select2-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="android_exact_blocked_version_inp" name="android_exact_blocked_version"
                                        value="{{ setting('android_exact_blocked_version') }}" />
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="android_exact_blocked_version">
                                    </div>

                                    <!--end::Select2-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class=" form-label">{{ __('Android Min Supported Version') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow "
                                        id="android_min_supported_version_inp" name="android_min_supported_version"
                                        value="{{ setting('android_min_supported_version') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="android_min_supported_version"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label for="android_maintenance_mode_inp"
                                        class=" form-label">{{ __('Android Maintenance Mode') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid filter-input" data-control="select2"
                                        data-hide-search="true" name="android_maintenance_mode"
                                        id="android_maintenance_mode_inp"
                                        data-placeholder="{{ __('Android Maintenance Mode') }}"
                                        data-dir="{{ isArabic() ? 'rtl' : 'ltr' }}">
                                        <option></option>
                                        <option {{ setting('android_maintenance_mode') == 1 ? 'selected' : '' }}
                                            value="1">
                                            {{ __('Yes') }}
                                        </option>
                                        <option {{ setting('android_maintenance_mode') == 0 ? 'selected' : '' }}
                                            value="0">
                                            {{ __('No') }}
                                        </option>
                                    </select>
                                    {{-- <input type="number" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="android_maintenance_mode_inp" name="android_maintenance_mode"
                                        value="{{ setting('android_maintenance_mode') }}" /> --}}
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="android_maintenance_mode">
                                    </div>

                                    <!--end::Select2-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class=" form-label">{{ __('Android Maintenance Message') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-solid mb-2 no-arrow"
                                        id="android_maintenance_message_inp" name="android_maintenance_message"
                                        value="{{ setting('android_maintenance_message') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="android_maintenance_message"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            {{-- ios --}}
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label for="ios_exact_blocked_version_inp"
                                        class=" form-label">{{ __('Ios Exact Blocked Version') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Select2-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="ios_exact_blocked_version_inp" name="ios_exact_blocked_version"
                                        value="{{ setting('ios_exact_blocked_version') }}" />
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="ios_exact_blocked_version">
                                    </div>

                                    <!--end::Select2-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class=" form-label">{{ __('Ios Min Supported Version') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow"
                                        id="ios_min_supported_version_inp" name="ios_min_supported_version"
                                        value="{{ setting('ios_min_supported_version') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="ios_min_supported_version"></div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <div class="d-flex flex-wrap gap-5" style="padding: 0 17rem">
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label for="ios_maintenance_mode_inp"
                                        class=" form-label">{{ __('Ios Maintenance Mode') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid filter-input" data-control="select2"
                                        data-hide-search="true" name="ios_maintenance_mode" id="ios_maintenance_mode_inp"
                                        data-placeholder="{{ __('Ios Maintenance Mode') }}"
                                        data-dir="{{ isArabic() ? 'rtl' : 'ltr' }}">
                                        <option></option>
                                        <option {{ setting('ios_maintenance_mode') == 1 ? 'selected' : '' }}
                                            value="1">
                                            {{ __('Yes') }}
                                        </option>
                                        <option {{ setting('ios_maintenance_mode') == 0 ? 'selected' : '' }}
                                            value="0">
                                            {{ __('No') }}
                                        </option>
                                    </select>
                                    {{-- <input type="number" step="0.1"
                                        class="form-control form-control-solid mb-2 no-arrow" id="ios_maintenance_mode_inp"
                                        name="ios_maintenance_mode" value="{{ setting('ios_maintenance_mode') }}" /> --}}
                                    <div class="fv-plugins-message-container invalid-feedback" id="ios_maintenance_mode">
                                    </div>

                                    <!--end::Select2-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row w-100 flex-md-root">
                                    <!--begin::Label-->
                                    <label class=" form-label">{{ __('Ios Maintenance Message') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control form-control-solid mb-2 no-arrow"
                                        id="ios_maintenance_message_inp" name="ios_maintenance_message"
                                        value="{{ setting('ios_maintenance_message') }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        id="ios_maintenance_message">
                                    </div>

                                    <!--end::Description-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Action buttons-->
                            <div class="row py-5">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">
                                            {{ __('Save Changes') }}
                                        </span>
                                        <span class="indicator-progress">
                                            {{ __('Please wait...') }} <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>

                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end:::Tab pane-->
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content-->
    <!-- begin :: Add role modal  -->
    <div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bolder">{{ __('Add role') }}</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                        onclick="$('#kt_modal_add_role').modal('hide')" data-kt-roles-modal-action="close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-lg-5 my-7">
                    <!--begin::Form-->
                    <form id="role_form_add" data-form-type="add" method="POST"
                        class="form ajax-form fv-plugins-bootstrap5 fv-plugins-framework"
                        action="{{ route('dashboard.admin.settings.roles.store') }}"
                        data-success-callback="onAjaxSuccess">
                        @csrf
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                            data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header"
                            data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px"
                            style="max-height: 637px;">

                            <!-- begin :: Row -->
                            <div class="row mb-8">

                                <!-- begin :: Column -->
                                <div class="col-md-6 fv-row">

                                    <label class="fs-5 fw-bold mb-2">{{ __('Name In Arabic') }}</label>
                                    <input type="text" class="form-control" name="name_ar" id="name_ar_inp" />
                                    <p class="invalid-feedback" id="name_ar"></p>
                                </div>
                                <!-- end   :: Column -->

                                <!-- begin :: Column -->
                                <div class="col-md-6 fv-row">

                                    <label class="fs-5 fw-bold mb-2">{{ __('Name In English') }}</label>
                                    <input type="text" class="form-control" name="name_en" id="name_en_inp" />
                                    <p class="invalid-feedback" id="name_en"></p>
                                </div>
                                <!-- end   :: Column -->

                            </div>
                            <!-- end   :: Row -->

                            <!--begin::Permissions-->
                            <div class="fv-row">

                                <div class="text-center m-auto" style="width:fit-content">
                                    <p class="bg-danger invalid-feedback text-white rounded p-2" id="abilities"></p>
                                </div>

                                <!--begin::Label-->
                                <label class="fs-5 fw-bolder form-label mb-2">{{ __('Permission validations') }}</label>
                                <!--end::Label-->
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">

                                            <!--begin::Table row-->
                                            <tr>
                                                <td class="text-gray-800">{{ __('Admin permissions') }}
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                        data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="{{ __('Allows full access to the system') }}"
                                                        aria-label="{{ __('Allows full access to the system') }}"></i>
                                                </td>
                                                <td>
                                                    <!--begin::Checkbox-->
                                                    <label class="form-check form-check-custom form-check-solid me-9">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="add-select-all" data-form-type="add">
                                                        <span class="form-check-label"
                                                            for="add-select-all">{{ __('Select all') }}</span>
                                                    </label>
                                                    <!--end::Checkbox-->
                                                </td>
                                            </tr>
                                            <!--end::Table row-->

                                            @foreach ($modules as $module)
                                                <tr>
                                                    <!--begin::Label-->
                                                    <td class="text-gray-800">
                                                        {{ __(ucfirst(str_replace('_', ' ', $module))) }}
                                                    </td>
                                                    <!--end::Label-->
                                                    <!--begin::Input group-->
                                                    <td>
                                                        <!--begin::Wrapper-->
                                                        <div class="d-flex">
                                                            @foreach ($abilities->where('category', $module) as $ability)
                                                                <!--begin::Checkbox-->
                                                                <label
                                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                                    <input class="form-check-input add-checkbox"
                                                                        type="checkbox" id="add_{{ $ability->name }}"
                                                                        data-id="{{ $ability->id }}"
                                                                        name="abilities[]">
                                                                    <label class="custom-control-label mx-4"
                                                                        for="add_{{ $ability->name }}">{{ __($ability->action) }}</label>
                                                                </label>
                                                                <!--end::Checkbox-->
                                                            @endforeach
                                                        </div>
                                                        <!--end::Wrapper-->
                                                    </td>
                                                    <!--end::Input group-->
                                                </tr>
                                            @endforeach

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Permissions-->
                        </div>
                        <!--end::Scroll-->
                        <!--begin::Actions-->
                        <div class="text-center pt-4">
                            <button type="submit" class="btn btn-primary" id="submit-btn"
                                data-kt-roles-modal-action="submit">
                                <span class="indicator-label">{{ __('Save') }}</span>
                                <span class="indicator-progress">{{ __('Please wait ...') }}
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!--end::Actions-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!-- end   :: Add role modal -->


    <!-- begin :: Update role modal -->
    <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bolder">{{ __('Edit role') }}</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                        onclick="$('#kt_modal_update_role').modal('hide')" data-kt-roles-modal-action="close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 my-7">
                    <!--begin::Form-->
                    <form id="role_form_update" data-form-type="update"
                        class="form fv-plugins-bootstrap5 fv-plugins-framework ajax-form" method="POST"
                        data-success-callback="onAjaxSuccess" data-trailing="_edit">
                        @csrf
                        @method('PUT')
                        <!--begin::Scroll-->
                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll"
                            data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                            data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header"
                            data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px"
                            style="max-height: 637px;">
                            <!--begin::Input group-->
                            <div class="fv-row mb-10 fv-plugins-icon-container">

                                <!-- begin :: Row -->
                                <div class="row mb-8">

                                    <!-- begin :: Column -->
                                    <div class="col-md-6 fv-row">

                                        <label class="fs-5 fw-bold mb-2">{{ __('Name in arabic') }}</label>
                                        <input type="text" class="form-control" id="name_ar_inp_edit"
                                            name="name_ar" />
                                        <p class="invalid-feedback" id="name_ar_edit"></p>


                                    </div>
                                    <!-- end   :: Column -->

                                    <!-- begin :: Column -->
                                    <div class="col-md-6 fv-row">

                                        <label class="fs-5 fw-bold mb-2">{{ __('Name in english') }}</label>
                                        <input type="text" class="form-control" id="name_en_inp_edit"
                                            name="name_en" />
                                        <p class="invalid-feedback" id="name_en_edit"></p>


                                    </div>
                                    <!-- end   :: Column -->

                                </div>
                                <!-- end   :: Row -->

                                <!--end::Input group-->
                                <!--begin::Permissions-->
                                <div class="fv-row">

                                    <div class="text-center m-auto" style="width:fit-content">
                                        <p class="bg-danger invalid-feedback text-white rounded p-2" id="abilities_edit">
                                        </p>
                                    </div>

                                    <!--begin::Label-->
                                    <label
                                        class="fs-5 fw-bolder form-label mb-2">{{ __('Permission validations') }}</label>
                                    <!--end::Label-->
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <!--begin::Table body-->
                                            <tbody class="text-gray-600 fw-bold">

                                                <!--begin::Table row-->
                                                <tr>
                                                    <td class="text-gray-800">{{ __('Admin permissions') }}
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="{{ __('Allows full access to the system') }}"
                                                            aria-label="{{ __('Allows full access to the system') }}"></i>
                                                    </td>
                                                    <td>
                                                        <!--begin::Checkbox-->
                                                        <label
                                                            class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="edit-select-all" data-form-type="update">
                                                            <span class="form-check-label"
                                                                for="edit-select-all">{{ __('Select all') }}</span>
                                                        </label>
                                                        <!--end::Checkbox-->
                                                    </td>
                                                </tr>
                                                <!--end::Table row-->

                                                @foreach ($modules as $module)
                                                    <tr>
                                                        <!--begin::Label-->
                                                        <td class="text-gray-800">
                                                            {{ __(ucfirst(str_replace('_', ' ', $module))) }}
                                                        </td>
                                                        <!--end::Label-->
                                                        <!--begin::Input group-->
                                                        <td>
                                                            <!--begin::Wrapper-->
                                                            <div class="d-flex">
                                                                @foreach ($abilities->where('category', $module) as $ability)
                                                                    <!--begin::Checkbox-->
                                                                    <label
                                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input edit-checkbox"
                                                                            type="checkbox"
                                                                            id="edit_{{ $ability->name }}"
                                                                            data-id="{{ $ability->id }}"
                                                                            name="abilities[]">
                                                                        <label class="custom-control-label mx-4"
                                                                            for="edit_{{ $ability->name }}">{{ __($ability->action) }}</label>
                                                                    </label>
                                                                    <!--end::Checkbox-->
                                                                @endforeach
                                                            </div>
                                                            <!--end::Wrapper-->
                                                        </td>
                                                        <!--end::Input group-->
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table wrapper-->
                                </div>
                                <!--end::Permissions-->
                            </div>
                            <!--end::Scroll-->
                        </div>

                        <!--begin::Actions-->
                        <div class="text-center pt-4 mt-1">
                            <button type="submit" class="btn btn-primary" id="submit-btn"
                                data-kt-roles-modal-action="submit">
                                <span class="indicator-label">{{ __('Save') }}</span>
                                <span class="indicator-progress">{{ __('Please wait ...') }}
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!--end::Actions-->

                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
@endsection

@push('scripts')
    <script>
        window.isArabic = '{{ isArabic() }}';
    </script>
    <script src="{{ asset('assets/js/forms/roles/common.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>

    <script>
        $("#add-role-btn").click(function() {
            $('.add-checkbox').prop('checked', false);
            removeValidationMessages();
        });

        window['onAjaxSuccess'] = () => {

            showToast();

            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }
    </script>
    <script>
        let language = locale == 'en' ? 'ltr' : 'rtl';
        tinymce.init({
            selector: "#privacy_policy_ar_inp",
            height: "480",
            menubar: false,
            toolbar: ["styleselect",
                "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
                "bullist numlist | outdent indent | ltr rtl | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
            ],
            directionality: language, // Set the initial direction to RTL if needed
            plugins: "advlist autolink link image lists charmap print preview code directionality"
        });
        tinymce.init({
            selector: "#privacy_policy_en_inp",
            height: "480",
            menubar: false,
            toolbar: ["styleselect",
                "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
                "bullist numlist | outdent indent | ltr rtl | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
            ],
            directionality: language, // Set the initial direction to RTL if needed
            plugins: "advlist autolink link image lists charmap print preview code directionality"
        });
        tinymce.init({
            selector: "#terms_ar_inp",
            height: "480",
            menubar: false,
            toolbar: ["styleselect",
                "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
                "bullist numlist | outdent indent | ltr rtl | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
            ],
            directionality: language, // Set the initial direction to RTL if needed
            plugins: "advlist autolink link image lists charmap print preview code directionality"
        });
        tinymce.init({
            selector: "#terms_en_inp",
            height: "480",
            menubar: false,
            toolbar: ["styleselect",
                "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
                "bullist numlist | outdent indent | ltr rtl | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
            ],
            directionality: language, // Set the initial direction to RTL if needed
            plugins: "advlist autolink link image lists charmap print preview code directionality"
        });
    </script>
@endpush
