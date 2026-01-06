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
        url: `/dashboard/admin/users/${userId}/interests`,
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
    var interests = response.interests.data || {};
    var productCards = "";
    productCards = `<div class="card card-flush py-4"><div class="d-flex gap-3" style="width: 100%;
    overflow: hidden;padding:10px 19px">`;

    if (Object.keys(interests).length > 0) {
        document
            .getElementById("no-results-alert")
            .style.setProperty("display", "none", "important");

        // $("#no-results-alert").hide();
        $.each(interests, function (index, product) {
            productCards += `

            <div class="border border-dashed border-gray-300 rounded p-5 mb-6">

                    <div class="d-flex align-items-center flex-wrap gap-1">
                        <!--begin::Badge-->
                        <span class="description-preview">
                    ${product.name}
                </span>
                        <!--end::Badge-->
                    </div>
                    <!--end::Header-->
                <!--end::Card body-->
            </div>
            `;
        });
        productCards += `</div></div>`;
    } else {
        if (response.total == 0) {
            // check if database contains products
            productCards = ``;
            $("#no-results-alert").fadeIn();
        }
    }
    $(".interests-container").html(productCards);
    paginator(response);
    KTMenu.createInstances();
};

var paginator = function (response) {
    var links = "";
    var paginationContent = "";
    var products = response.interests.data || [];
    var paginationData = response.interests;
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
    $("#interests-container").html("");
    $("#loading-alert").removeClass("d-none");
}
function hideLoading() {
    $("#loading-alert").addClass("d-none");
}
