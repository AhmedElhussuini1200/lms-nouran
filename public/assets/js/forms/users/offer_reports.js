"use strict";

var datatable;
document
    .getElementById("pagination-container")
    .style.setProperty("display", "none", "important");
document
    .getElementById("offer-container")
    .style.setProperty("display", "none", "important");
$("#no-results-alert").hide();
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "users";
    // Private functions
    var initDatatable = function () {
        if ($.fn.DataTable.isDataTable("#kt_datatable_offers_reports")) {
            $("#kt_datatable_offers_reports").DataTable().destroy();
        }
        datatable = $("#kt_datatable_offers_reports").DataTable({
            language: language,
            searchDelay: searchDelay,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            ajax: {
                url: `/dashboard/admin/${prefix}/${dbTable}/${userId}/offers_reports`,
            },
            columns: [
                // { data: 'id' },
                { data: "user.full_name" },
                { data: "offer.mission.description" },
                { data: "offer.available_budget" },
                { data: "report.name" },
                { data: "details" },
                { data: "created_at" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                },
                {
                    targets: 1,
                    orderable: false,
                    render: function (data, type, row) {
                        return `<a     onclick="showMoreInDT(this)" > <span  class="cursor-pointer" title="${__(
                            "show more"
                        )}" >${data.substr(0, 20)}</span> </a>
                                <span  title="${__("show more")}"> ${
                            data.length > 20 ? "..." : ""
                        } </span>
                                <span  style="display:none"> ${data.substr(
                                    20
                                )} </span>`;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <!--begin::Info-->
                                <div class="d-flex justify-content-center" dir="ltr">
                                <img alt="riyal"
                                                        src="${riyalLogoUrl}"
                                                        class="w-20px h-20px ms-2 me-2" />
                                    <div class="mb-1 text-gray-600">${Number(
                                        data
                                    ).toLocaleString()}</div>
                                </div>
                                <!--end::Info-->
                            </div>
                        `;
                    },
                },
                {
                    targets: 4,
                    orderable: false,
                    render: function (data, type, row) {
                        if (!data) {
                            // لو null أو undefined أو string فاضي
                            return `<span class="text-muted">-</span>`;
                        }

                        return `
            <a onclick="showMoreInDT(this)">
                <span class="cursor-pointer" title="${__("show more")}">
                    ${data.substr(0, 20)}
                </span>
            </a>
            <span title="${__("show more")}">
                ${data.length > 20 ? "..." : ""}
            </span>
            <span style="display:none">
                ${data.substr(20)}
            </span>`;
                    },
                },
            ],
            // Add data-filter attribute
            createdRow: function (row, data, dataIndex) {
                // $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            },
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on("draw", function () {
            KTMenu.createInstances();
        });
    };

    // Public methods
    return {
        init: function () {
            initDatatable();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
