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
        if ($.fn.DataTable.isDataTable("#kt_datatable_reviews")) {
            $("#kt_datatable_reviews").DataTable().destroy();
        }
        datatable = $("#kt_datatable_reviews").DataTable({
            language: language,
            searchDelay: searchDelay,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            ajax: {
                url: `/dashboard/admin/${prefix}/${dbTable}/${userId}`,
            },
            columns: [
                // { data: 'id' },
                { data: "user_owner.full_name" },
                { data: "mission.description" },
                { data: "rate" },
                { data: "comment" },
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
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <!--begin::Info-->
                                <div class="d-flex flex-column justify-content-center">
                                    <div class="rating">
                                    ${[1, 2, 3, 4, 5]
                                        .map(
                                            (i) => `
                                        <div class="rating-label ${
                                            i <= Math.floor(data)
                                                ? "checked"
                                                : ""
                                        }">
                                            <i class="ki-outline ki-star fs-6"></i>
                                        </div>
                                    `
                                        )
                                        .join("")}
                                </div>
                                </div>
                                <!--end::Info-->
                            </div>
                        `;
                    },
                },
                {
                    targets: 3,
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
