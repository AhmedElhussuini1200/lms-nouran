$(document).ready(function () {
    document
        .getElementById("offer-container")
        .style.setProperty("display", "none", "important");

    retrieveCertificatesFormBackend();

    $("#filter-form").submit(function (e) {
        e.preventDefault();
        retrieveCertificatesFormBackend();
    });

    $("input[name='name']").keyup(function (e) {
        retrieveCertificatesFormBackend();
    });

    pageTransition();
});

function retrieveCertificatesFormBackend(page = 1) {
    let form = document.getElementById("filter-form");

    showLoading();
    $.ajax({
        type: "get",
        url: `/dashboard/admin/users/${userId}/portfolios`,
        data: $(form).serialize(),
        success: function (response) {
            hideLoading();
            console.log(response);
            portfoliosItems(response);
        },
        error: function (response) {
            hideLoading();
            console.log(response);
        },
    });
}

var portfoliosItems = function (response) {
    var portfolios = response.portfolios.data || {};
    var portfolioCards = "";
    if (portfolios.length > 0) {
        portfolioCards = `
        <div  id="portfolios-grid">
        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">`;
        document
            .getElementById("no-results-alert")
            .style.setProperty("display", "none", "important");
        portfolios.forEach((portfolio, index) => {
            const maxLength = 50;
            const fullDesc = portfolio.description || "";
            const isLong = fullDesc.length > maxLength;
            const shortDesc = isLong
                ? fullDesc.slice(0, maxLength) + "..."
                : fullDesc;

            let descriptionHtml = `
    <span class="description-preview" id="desc-${portfolio.id}">
        ${isLong ? shortDesc : fullDesc}
    </span>`;

            if (isLong) {
                descriptionHtml += `
        <a href="javascript:void(0);" class="fs-8 text-semibold ms-2 toggle-desc"
            data-id="${portfolio.id}" data-full="${fullDesc.replace(
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
            portfolioCards += `
            <div class="col-md-6 col-lg-4 col-xl-3" id="${index + 1}">
            <!--begin::Card-->
            <div class="card h-100">
                <!--begin::Card body-->
                <div class="card-body card-custom d-flex justify-content-center text-center flex-column p-8"
                    data-bs-toggle="collapse"
                     style="cursor: pointer;">
                    <!--begin::Name-->
                    <a href="#portfolio-${portfolio.id}"
                        class="text-gray-800 text-hover-primary d-flex flex-column portfolio-click" data-portfolio-id="${
                            portfolio.id
                        }">
                        <!--begin::Image-->
                        <div class="symbol symbol-75px mb-5">
                            <img src="/assets/media/svg/files/folder-document.svg"
                                class="theme-light-show" alt="" />
                            <img src="/assets/media/svg/files/folder-document-dark.svg"
                                class="theme-dark-show" alt="" />
                        </div>
                        <!--end::Image-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bold mb-2">${portfolio.title}</div>
                        <!--end::Title-->
                    </a>
                    <!--end::Name-->
                    <div class="fs-7 fw-semibold mb-2">
                        ${descriptionHtml}
                    </div>
                    <!--begin::Description-->
                    <div class="fs-7 fw-semibold text-gray-500 mb-6">
                         ${portfolio.files.length} ${__("files")}</div>
                    <!--end::Description-->

                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>`;
        });
        portfolioCards += `
        </div> <!-- close .row -->
      </div> <!-- close #portfolios-grid -->
      <div id="portfolio-details" style="display: none;"></div>
      `;
    } else {
        if (response.total == 0) {
            // check if database contains products
            portfolioCards = ``;
            $("#no-results-alert").fadeIn();
        }
    }

    $(".portfolios-container").html(portfolioCards);
    $(".portfolio-click")
        .off("click")
        .on("click", function () {
            const certId = $(this).data("portfolio-id");

            $("#portfolios-grid").hide();
            $("#pagination-container").hide();

            $("#portfolio-details").html(""); // Clear previous content

            const cert = portfolios.find((c) => c.id === certId);
            if (cert) {
                let filesHtml = `
        <div class="mb-4">
            <!--begin::Table header-->
                <div class="d-flex flex-stack mb-4 back-to-portfolios">
                    <!--begin::Folder path-->
                    <div class="badge badge-lg "style="background: #002F63;">
                        <div class="d-flex align-items-center flex-wrap">
                        <i class="ki-outline ki-abstract-32 fs-2x text-white me-3"></i>
                        <a href="#" class="text-white" >${__("Portfolios")}</a>
                        <i class="ki-outline ki-right fs-2x text-white mx-1"></i>
                        <a href="#" class="text-white">${cert.title}</a>
                    </div>
                    </div>
                    <!--end::Folder path-->
                </div>
            <!--end::Table header-->

            <div class="row g-6">`;

                cert.files.forEach((file) => {
                    const extension = file.file.split(".").pop().toLowerCase();
                    const filename = file.file.split("/").pop();
                    const nameOnly = filename.includes("_")
                        ? filename.split("_").pop().split(".")[0]
                        : filename;

                    let icon = "file.svg";
                    if (extension === "pdf") icon = "pdf.svg";
                    else if (["jpg", "jpeg"].includes(extension))
                        icon = "jpg.png";
                    else if (extension === "png") icon = "png.png";

                    filesHtml += `
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <a href="${file.full_image_path}" target="_blank" class="text-gray-800 text-hover-primary d-flex flex-column">
                                <div class="symbol symbol-60px mb-5">
                                    <img src="/assets/media/svg/files/${icon}" alt="">
                                </div>
                                <div class="fs-5 fw-bold mb-2">${nameOnly}</div>
                            </a>
                            <div class="fs-7 fw-semibold text-gray-500">${file.created_at}</div>
                        </div>
                    </div>
                </div>`;
                });

                filesHtml += `</div></div>`;
                $("#portfolio-details").html(filesHtml).fadeIn();
            }
        });

    $(document).on("click", ".back-to-portfolios", function () {
        $("#portfolio-details").hide();
        $("#portfolios-grid").fadeIn();
    });
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
    // window.products = products;
};
var paginator = function (response) {
    var links = "";
    var paginationContent = "";
    var portfolios = response.portfolios.data || [];
    var paginationData = response.portfolios;
    var prevUrl = paginationData.prev_page_url || "javascript:;";
    var nextUrl = paginationData.next_page_url || "javascript:;";

    if (portfolios.length != 0) {
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
                licensesItems(response);
            });
        }
    });
};
function showLoading() {
    $("#portfolios-container").html("");
    $("#loading-alert").removeClass("d-none");
}
function hideLoading() {
    $("#loading-alert").addClass("d-none");
}
