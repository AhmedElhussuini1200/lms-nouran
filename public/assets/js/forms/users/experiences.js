$(document).ready(function () {
    document
        .getElementById("offer-container")
        .style.setProperty("display", "none", "important");

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

function retrieveProductsFormBackend(page = 1) {
    let form = document.getElementById("filter-form");
    let isAdvancedSearch = $("#kt_advanced_search_form").hasClass("show");

    $("input[name='advanced_search']").val(isAdvancedSearch);
    showLoading();
    $.ajax({
        type: "get",
        url: `/dashboard/admin/users/${userId}/experiences`,
        data: $(form).serialize(),
        success: function (response) {
            hideLoading();
            productItems(response);
        },
        error: function (response) {
            hideLoading();
        },
    });
}

var productItems = function (response) {
    var experiences = response.experiences.data || {};
    var productCards = "";
    productCards = `<div class="d-flex gap-3">`;

    if (Object.keys(experiences).length > 0) {
        document
            .getElementById("no-results-alert")
            .style.setProperty("display", "none", "important");

        // $("#no-results-alert").hide();
        $.each(experiences, function (index, product) {
            const maxLength = 50;
            const fullDesc = product.description || "";
            const isLong = fullDesc.length > maxLength;
            const shortDesc = isLong
                ? fullDesc.slice(0, maxLength) + "..."
                : fullDesc;
            let descriptionHtml = `
                <span class="description-preview" id="desc-${product.id}">
                    ${isLong ? shortDesc : fullDesc}
                </span>`;

            if (isLong) {
                descriptionHtml += `
        <a href="javascript:void(0);" class="fs-8 text-semibold ms-2 toggle-desc"
            data-id="${product.id}" data-full="${fullDesc.replace(
                    /"/g,
                    "&quot;"
                )}"
            data-short="${shortDesc.replace(
                /"/g,
                "&quot;"
            )}" style="color: gray">
            (${__("Show more")})
        </a>`;
            }
            productCards += `
            <div class="card mb-6 mb-xl-9 col-md-3">
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Title-->
                    <div class="d-flex align-items-center mb-3 gap-2">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;font-size: 16px;  background: #002F63">
                        ${index + 1}
                    </div>
                        <span class="mb-2"style="width: 100%;font-weight: 500 !important;
font-size: 17px !important;
line-height: 100%;
">${product.title}</span>
                    <!--end::Title-->
                    </div>
                    <!--begin::Header-->
                    <div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-1">
                        <!--begin::Badge-->
                        ${descriptionHtml}
                        <!--end::Badge-->
                    </div>
                    <!--end::Header-->
                </div>
                <!--end::Card body-->
            </div>
            `;
        });
        productCards += `</div>`;
    } else {
        if (response.total == 0) {
            // check if database contains products
            productCards = ``;
            $("#no-results-alert").fadeIn();
        }
    }
    $(".experiences-container").html(productCards);
    $(document)
        .off("click", ".toggle-desc")
        .on("click", ".toggle-desc", function () {
            const id = $(this).data("id");
            const full = $(this).data("full");
            const short = $(this).data("short");
            const target = $(`#desc-${id}`);
            const link = $(this);

            console.log("Toggle clicked", { id, full, short });

            if (link.text().includes("Show more")) {
                target.text(full);
                link.text(`(${__("Show less")})`);
            } else {
                target.text(short);
                link.text(`(${__("Show more")})`);
            }
        });
    paginator(response);
    KTMenu.createInstances();
};

var paginator = function (response) {
    var links = "";
    var paginationContent = "";
    var products = response.experiences.data || [];
    var paginationData = response.experiences;
    var prevUrl = paginationData.prev_page_url || "javascript:;";
    var nextUrl = paginationData.next_page_url || "javascript:;";
    if (products.length != 0) {
        for (var i = 1; i <= paginationData.last_page; i++) {
            var isCurrentPage = paginationData.current_page == i;
            var activeClass = isCurrentPage ? "active" : "";

            if (paginationData.links[i] !== undefined) {
                // Append filter parameters to the pagination URLs
                var pageUrl = paginationData.links[i].url;
                links += `
                <li class="page-item ${activeClass}">
                    <a href="${
                        isCurrentPage ? "#" : pageUrl
                    }" class="page-link">${i}</a>
                </li>
                `;
            }
        }

        var prevPageUrl = prevUrl !== "javascript:;" ? prevUrl : "javascript:;";
        var nextPageUrl = nextUrl !== "javascript:;" ? nextUrl : "javascript:;";

        paginationContent = `
        <div class="spinner-border spinner-border-sm my-auto d-none" id="pagination-loading" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <li class="page-item previous ${
            prevUrl == "javascript:;" ? "disabled" : ""
        }">
            <a href="${prevPageUrl}" class="page-link">
                <i class="previous"></i>
            </a>
        </li>
        ${links}
        <li class="page-item next ${
            nextUrl == "javascript:;" ? "disabled" : ""
        }">
            <a href="${nextPageUrl}" class="page-link">
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
        document
            .getElementById("pagination-container")
            .style.setProperty("display", "none", "important");
        // $("#pagination-container").hide();
    }
    $(".pagination").html(paginationContent);
};

var pageTransition = function () {
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();

        var url = $(this).attr("href");

        if (url != "#") {
            $("#pagination-loading").removeClass("d-none");
            // Use the serialized filter form data in the AJAX request
            $.get(url, function (response) {
                $("#pagination-loading").addClass("d-none");
                productItems(response);
            });
        }
    });
};

function showLoading() {
    $("#experiences-container").html("");
    $("#loading-alert").removeClass("d-none");
}
function hideLoading() {
    $("#loading-alert").addClass("d-none");
}
