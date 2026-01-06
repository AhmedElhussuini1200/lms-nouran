$(document).ready(function () {
    retrieveProductsFormBackend();

    $("#filter-form").submit(function (e) {
        e.preventDefault();
        retrieveProductsFormBackend();
    });

    $("input[name='name']").keyup(function (e) {
        retrieveProductsFormBackend();
    });

    pageTransition();
});

function retrieveProductsFormBackend() {
    let form = document.getElementById("filter-form");
    let isAdvancedSearch = $("#kt_advanced_search_form").hasClass("show");

    $("input[name='advanced_search']").val(isAdvancedSearch);
    showLoading();
    $.ajax({
        type: "get",
        url: "/dashboard/admin/products",
        data: $(form).serialize(),
        success: function (response) {
            hideLoading();
            console.log(response);
            productItems(response);
        },
        error: function (response) {
            hideLoading();
            console.log(response);
        },
    });
}

var productItems = function (response) {
    var products = response.products.data || {};
    var productCards = "";

    productCards = `
        <!--begin::Col-->
        <div class="col-md-6 col-xxl-4">
            <!--begin::Card-->
            <div class="card" style="height: 100%;">
                <!--begin::Card body-->
                <div class="card-body d-flex flex-center flex-column">
                    <!--begin::Button-->
                    <a href="/dashboard/admin/products/create" class="btn btn-clear d-flex flex-column flex-center">
                        <!--begin::Illustration-->
                        <img src="/assets/vendor-dashboard/media/illustrations/sketchy-1/13.png" alt=""
                            class="mw-100 mh-175px">
                        <!--end::Illustration-->
                        <!--begin::Label-->
                        <div class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">
                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2022-11-29-094551/core/html/src/media/icons/duotune/general/gen041.svg-->
                            <span class="svg-icon svg-icon-muted svg-icon-2x"><svg width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor" />
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1"
                                        transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            ${__("Create new product")}
                        </div>
                        <!--end::Label-->
                    </a>
                    <!--begin::Button-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
        `;

    if (Object.keys(products).length > 0) {
        $("#no-results-alert").hide();
        $.each(products, function (index, product) {
            let accountStatus;
            let productRateIcon;
            if (product.status === "In Stock")
                accountStatus = "badge-light-success";
            else accountStatus = "badge-light-danger";

            productCards += `
            <div class="col-md-6 col-xl-4" id="card-${product.id}">
                <!--begin::Card-->
                <div class="card border-hover-primary" style="height: 100%;">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-9">
                        <!--begin::Card Title-->
                        <div class="card-title m-0">
                            <!--begin::Avatar-->
                            <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                <div class="rounded overflow-hidden me-3 w-100px h-100px w-lg-150px h-lg-150px">
                                    <div class="fs-2 fw-semibold text-success"><img src="${
                                        product?.images[0]?.full_image_path
                                    }"  class="img-fluid"></div>
                                    </div>
                            </div>
                            <!--end::Avatar-->
                             <div class="col-md-6 col-xl-5">
                                <a href="/dashboard/admin/products/${
                                    product.id
                                }" class="text-gray-900 text-hover-primary fs-2 fw-bold">${
                product.name
            }</a>
                             </div>
                            <!--begin::Card toolbar-->
                             <div class="col-md-6 col-xl-1">
                            <div class="card-toolbar">
                                <div class="me-0">
                                    <button type="button"
                                        class="btn btn-clean btn-sm btn-icon btn-icon-primary btn-active-light-primary dropdown-btn me-n3"
                                        data-kt-menu-trigger="hover" data-kt-menu-placement="bottom-end">
                                        <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2022-12-26-231111/core/html/src/media/icons/duotune/coding/cod001.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-primary" style="color: #DEB893"><svg width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </button>
                                    <!--begin::Menu 3-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                        data-kt-menu="true">
                                        <!--begin::Heading-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">${__(
                                                "Actions"
                                            )}</div>
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="/dashboard/admin/products/${
                                                product.id
                                            }"
                                                class="menu-link flex-stack px-3">${__(
                                                    "Product data"
                                                )}
                                                <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2022-12-26-231111/core/html/src/media/icons/duotune/general/gen045.svg-->
                                                <span class="svg-icon svg-icon-muted svg-icon-2">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                                            fill="currentColor" />
                                                        <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)"
                                                            fill="currentColor" />
                                                        <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="/dashboard/admin/products/${
                                                product.id
                                            }/edit"
                                                class="menu-link flex-stack px-3">${__(
                                                    "Edit"
                                                )}
                                                <span class="svg-icon svg-icon-muted svg-icon-2"><svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.4" d="M15.48 3H7.52C4.07 3 2 5.06 2 8.52V16.47C2 19.94 4.07 22 7.52 22H15.47C18.93 22 20.99 19.94 20.99 16.48V8.52C21 5.06 18.93 3 15.48 3Z" fill="currentColor"/>
<path d="M21.0195 2.97979C19.2295 1.17979 17.4795 1.13979 15.6395 2.97979L14.5095 4.09979C14.4095 4.19979 14.3795 4.33979 14.4195 4.46979C15.1195 6.91979 17.0795 8.87979 19.5295 9.57979C19.5595 9.58979 19.6095 9.58979 19.6395 9.58979C19.7395 9.58979 19.8395 9.54979 19.9095 9.47979L21.0195 8.35979C21.9295 7.44979 22.3795 6.57979 22.3795 5.68979C22.3795 4.78979 21.9295 3.89979 21.0195 2.97979Z" fill="currentColor"/>
<path d="M17.8591 10.4198C17.5891 10.2898 17.3291 10.1598 17.0891 10.0098C16.8891 9.88984 16.6891 9.75984 16.4991 9.61984C16.3391 9.51984 16.1591 9.36984 15.9791 9.21984C15.9591 9.20984 15.8991 9.15984 15.8191 9.07984C15.5091 8.82984 15.1791 8.48984 14.8691 8.11984C14.8491 8.09984 14.7891 8.03984 14.7391 7.94984C14.6391 7.83984 14.4891 7.64984 14.3591 7.43984C14.2491 7.29984 14.1191 7.09984 13.9991 6.88984C13.8491 6.63984 13.7191 6.38984 13.5991 6.12984C13.4691 5.84984 13.3691 5.58984 13.2791 5.33984L7.89912 10.7198C7.54912 11.0698 7.20912 11.7298 7.13912 12.2198L6.70912 15.1998C6.61912 15.8298 6.78912 16.4198 7.17912 16.8098C7.50912 17.1398 7.95912 17.3098 8.45912 17.3098C8.56912 17.3098 8.67912 17.2998 8.78912 17.2898L11.7591 16.8698C12.2491 16.7998 12.9091 16.4698 13.2591 16.1098L18.6391 10.7298C18.3891 10.6498 18.1391 10.5398 17.8591 10.4198Z" fill="currentColor"/>
</svg></span></a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link flex-stack px-3 delete-item" data-id="${
                                                product.id
                                            }">${__("Delete")}
                                               <span class="svg-icon svg-icon-muted svg-icon-2"><svg width="800px" height="800px" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <path d="M667.8 362.1H304V830c0 28.2 23 51 51.3 51h312.4c28.4 0 51.4-22.8 51.4-51V362.2h-51.3z" fill="currentColor" />
    <path
        d="M750.3 295.2c0-8.9-7.6-16.1-17-16.1H289.9c-9.4 0-17 7.2-17 16.1v50.9c0 8.9 7.6 16.1 17 16.1h443.4c9.4 0 17-7.2 17-16.1v-50.9z"
        fill="currentColor" />
    <path
        d="M733.3 258.3H626.6V196c0-11.5-9.3-20.8-20.8-20.8H419.1c-11.5 0-20.8 9.3-20.8 20.8v62.3H289.9c-20.8 0-37.7 16.5-37.7 36.8V346c0 18.1 13.5 33.1 31.1 36.2V830c0 39.6 32.3 71.8 72.1 71.8h312.4c39.8 0 72.1-32.2 72.1-71.8V382.2c17.7-3.1 31.1-18.1 31.1-36.2v-50.9c0.1-20.2-16.9-36.8-37.7-36.8z m-293.5-41.5h145.3v41.5H439.8v-41.5z m-146.2 83.1H729.5v41.5H293.6v-41.5z m404.8 530.2c0 16.7-13.7 30.3-30.6 30.3H355.4c-16.9 0-30.6-13.6-30.6-30.3V382.9h373.6v447.2z"
        fill="currentColor" />
    <path
        d="M511.6 798.9c11.5 0 20.8-9.3 20.8-20.8V466.8c0-11.5-9.3-20.8-20.8-20.8s-20.8 9.3-20.8 20.8v311.4c0 11.4 9.3 20.7 20.8 20.7zM407.8 798.9c11.5 0 20.8-9.3 20.8-20.8V466.8c0-11.5-9.3-20.8-20.8-20.8s-20.8 9.3-20.8 20.8v311.4c0.1 11.4 9.4 20.7 20.8 20.7zM615.4 799.6c11.5 0 20.8-9.3 20.8-20.8V467.4c0-11.5-9.3-20.8-20.8-20.8s-20.8 9.3-20.8 20.8v311.4c0 11.5 9.3 20.8 20.8 20.8z"
        fill="currentColor" />
</svg></span></span></a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu 3-->
                                </div>
                            </div>
                            </div>
                        <!--end::Card toolbar-->
                        </div>
                        <!--end::Car Title-->

                    </div>
                    <!--end:: Card header-->
                    <!--begin:: Card body-->
                    <div class="card-body p-9 pt-4"style="display: flex;flex-direction: column;justify-content: flex-end;">

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap flex-row-reverse justify-content-between fw-semibold fs-6 mb-6 pe-4">
                            <a href="javascript:;" class="d-flex align-items-center text-gray-400 me-5 mb-2 cursor-default disabled">
                                <span class="svg-icon svg-icon-muted svg-icon-2">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
    <path
        d="M5 6.2C5 5.07989 5 4.51984 5.21799 4.09202C5.40973 3.71569 5.71569 3.40973 6.09202 3.21799C6.51984 3 7.07989 3 8.2 3H15.8C16.9201 3 17.4802 3 17.908 3.21799C18.2843 3.40973 18.5903 3.71569 18.782 4.09202C19 4.51984 19 5.07989 19 6.2V21L12 16L5 21V6.2Z"
        stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
</svg></span>

                                <!--end::Svg Icon-->${__("" + product.type)}
                            </a>
                            <a href="javascript:;" class="d-flex align-items-center text-gray-400 me-5 mb-2 cursor-default disabled">
                                <span class="svg-icon svg-icon-muted svg-icon-2">
<svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24" width="512" fill="currentColor" height="512">
    <path
        d="M23.271,9.419C21.72,6.893,18.192,2.655,12,2.655S2.28,6.893.729,9.419a4.908,4.908,0,0,0,0,5.162C2.28,17.107,5.808,21.345,12,21.345s9.72-4.238,11.271-6.764A4.908,4.908,0,0,0,23.271,9.419Zm-1.705,4.115C20.234,15.7,17.219,19.345,12,19.345S3.766,15.7,2.434,13.534a2.918,2.918,0,0,1,0-3.068C3.766,8.3,6.781,4.655,12,4.655s8.234,3.641,9.566,5.811A2.918,2.918,0,0,1,21.566,13.534Z" />
    <path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z" />
</svg>
                                </span>

                                <!--end::Svg Icon--> ${
                                    product.hidden_flag == 1
                                        ? __("Visible")
                                        : __("Invisible")
                                }
                            </a>
                            <a href="javascript:;" class="d-flex align-items-center text-gray-400 me-5 mb-2 cursor-default disabled">
                                <span class="svg-icon svg-icon-muted svg-icon-2">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M13.2252 6.36449L12.653 8.50002H14.6867L15.363 5.97626L16.8118 6.36449L16.2396 8.50002H17.5V10H15.8377L15.3018 12H17V13.5H14.8999L14.2237 16.0237L12.7748 15.6355L13.347 13.5H11.3132L10.637 16.0237L9.1881 15.6355L9.7603 13.5H8.5V12H10.1622L10.6981 10H9V8.50002H11.1L11.7763 5.97626L13.2252 6.36449ZM11.7151 12H13.7489L14.2848 10H12.251L11.7151 12Z" fill="currentColor"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M5 3H21V19H5V3ZM6.5 4.5H19.5V17.5H6.5V4.5Z" fill="currentColor"/>
<path d="M2 6V22H18V20.5H3.5V6H2Z" fill="currentColor"/>
</svg>
                                </span>

                                <!--end::Svg Icon-->${product.serial_no ?? "--"}
                            </a>
                        </div>
                        <div class="d-flex flex-wrap">
                            <!--begin::Due-->
                            <div class="border border-gray-300 border-dashed rounded py-2 px-4 me-2 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${
                                    product.created_at
                                }</div>
                                <div class="fw-semibold text-gray-400">${__(
                                    "Created at"
                                )}</div>
                            </div>
                            <!--end::Due-->
                            <div class="border border-gray-300 border-dashed rounded py-2 me-2 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${
                                    product.caliber ?? `-`
                                }</div>
                                <div class="fw-semibold text-gray-400">${__(
                                    "Product caliber"
                                )}</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded py-2 me-2 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${
                                    product.price_change ? __("Yes") : __("No")
                                }</div>
                                <div class="fw-semibold text-gray-400">${__(
                                    "Auto update price"
                                )}</div>
                            </div>
                            <!--end::Budget-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end:: Card body-->
                </div>
                <!--end::Card-->
            </div>
            `;
        });
    } else {
        if (response.total > 0) {
            // check if database contains products
            productCards = ``;
            $("#no-results-alert").fadeIn();
        }
    }

    $(".products-container").html(productCards);
    deleteProduct();
    paginator(response);
    KTMenu.createInstances();
    window.products = products;
};

var paginator = function (response) {
    var links = "";
    var paginationContent = "";
    var products = response.products.data || [];
    var paginationData = response.products;
    var prevUrl = paginationData.prev_page_url || "javascript:;";
    var nextUrl = paginationData.next_page_url || "javascript:;";

    if (products.length != 0) {
        for (var i = 1; i <= paginationData.last_page; i++) {
            var isCurrentPage = paginationData.current_page == i;
            var activeClass = isCurrentPage ? "active" : "";

            if (paginationData.links[i] !== undefined) {
                links += `
                <li class="page-item ${activeClass}">
                    <a href="${
                        isCurrentPage ? "#" : paginationData.links[i].url
                    }" class="page-link">${i}</a>
                </li>
                `;
            }
        }
        paginationContent = `
        <div class="spinner-border spinner-border-sm my-auto d-none" id="pagination-loading" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <li class="page-item previous ${
            prevUrl == "javascript:;" ? "disabled" : ""
        }">
            <a href="${prevUrl}" class="page-link">
                <i class="previous"></i>
            </a>
        </li>
        ${links}
        <li class="page-item next ${
            nextUrl == "javascript:;" ? "disabled" : ""
        }">
            <a href="${nextUrl}" class="page-link">
                <i class="next"></i>
            </a>
        </li>
        `;

        $(".pagination-info").text(
            __(`Show 1 to`) +
                ` ${paginationData.per_page} ` +
                __(`from total`) +
                ` ${paginationData.total} `
        );

        $("#pagination-container").show();
    } else {
        $("#pagination-container").hide();
    }

    $(".pagination").html(paginationContent);
};

var pageTransition = function () {
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();

        var url = $(this).attr("href");

        if (url != "#") {
            $("#pagination-loading").removeClass("d-none");

            $.get(url, $("#filterForm").serialize(), function (response) {
                $("#pagination-loading").addClass("d-none");
                productItems(response);
            });
        }
    });
};

function showLoading() {
    $("#products-container").html("");
    $("#loading-alert").removeClass("d-none");
}
function hideLoading() {
    $("#loading-alert").addClass("d-none");
}

function deleteProduct(params) {
    $(".delete-item").on("click", function (e) {
        e.preventDefault();

        let id = $(this).attr("data-id");
        deleteElement("", `/dashboard/admin/products/${id}`, () =>
            retrieveProductsFormBackend()
        );
    });
}

function handlePreviewClick(imagePath) {
    // Clear the current attachments preview
    $(".attachments").html("");

    // Append the new attachment to be previewed
    $(".attachments").append(`
        <!--begin::Overlay-->
        <a class="d-block overlay" data-fslightbox="lightbox-basic" href="${imagePath}">
            <!--begin::Action-->
            <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                <i class="bi bi-eye-fill text-white fs-3x"></i>
            </div>
            <!--end::Action-->
        </a>
        <!--end::Overlay-->
    `);

    // Refresh lightbox instance to ensure the new content is recognized
    refreshFsLightbox();

    // Automatically trigger the first attachment preview (lightbox)
    $("[data-fslightbox='lightbox-basic']:first").trigger("click");
}
