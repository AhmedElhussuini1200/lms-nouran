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
        if ($.fn.DataTable.isDataTable("#kt_datatable_reports")) {
            $("#kt_datatable_reports").DataTable().destroy();
        }
        datatable = $("#kt_datatable_reports").DataTable({
            language: language,
            searchDelay: searchDelay,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            ajax: {
                url: `/dashboard/admin/${prefix}/${dbTable}/${userId}/reports`,
            },
            columns: [
                // { data: 'id' },
                { data: "user.full_name" },
                { data: "report.name" },
                { data: "reason" },
                { data: "created_at" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                },
                {
                    targets: 2,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                           <div>
                                <!--begin::Info-->
                                <div class="d-flex flex-column justify-content-center" >
                                    <span class="mb-1 text-gray-600" >${
                                        data ?? "--"
                                    }</span>
                                </div>
                                <!--end::Info-->
                            </div>
                        `;
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
