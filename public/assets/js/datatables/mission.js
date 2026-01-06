"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "missions";
    let prefix = userType;
    // Private functions
    var initDatatable = function () {
        datatable = $("#kt_datatable").DataTable({
            language: language,
            searchDelay: searchDelay,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            select: {
                style: "multi",
                selector: 'td:first-child input[type="checkbox"]',
                className: "row-selected",
            },
            ajax: {
                url: `/dashboard/${prefix}/${dbTable}`,
                data: function (d) {
                    if (pageType && pageType !== "all") {
                        d.status = pageType; // "pending" or "approved" cancelled
                    }
                    d.priorityFilter = $("#priority_filter").val();
                    d.statusFilter = $("#status_filter").val();
                },
            },

            columns: [
                { data: "id" },
                { data: "complaint_number" },
                { data: "customer_name" },
                { data: "address" },
                { data: "street" },
                { data: "plot_number" },
                { data: "status" },
                { data: "priority" },
                { data: "approved_by" },
                { data: "created_at" },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${data}" />
                            </div>`;
                    },
                },
                {
                    targets: 1,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${row.complaint_number}</span>`,
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return `
                            <div>
                                ${row.customer_name}
                                <div class="fw-semibold fs-6 text-gray-400">${row.customer_phone}</div>
                            </div>
                        `;
                    },
                },
                {
                    targets: 3,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${row.address}</span>`,
                },
                {
                    targets: 4,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${row.street}</span>`,
                },
                {
                    targets: 5,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${row.plot_number}</span>`,
                },
                {
                    targets: 6,
                    orderable: false,
                    render: (data, type, row) => {
                        if (!row.status) return "--";
                        return `
                            <span class="badge"
                                style="background-color:${
                                    row.status.color ?? "#ccc"
                                }; color:#fff;">
                                ${row.status.name}
                            </span>`;
                    },
                },
                {
                    targets: 7,
                    orderable: false,
                    render: (data, type, row) => {
                        if (!row.priority) return "--";
                        return `
                            <span class="badge"
                                style="background-color:${
                                    row.priority.color ?? "#ccc"
                                }; color:#fff;">
                                ${row.priority.name}
                            </span>`;
                    },
                },
                {
                    targets: 8,
                    orderable: false,
                    render: (data, type, row) => {
                        return `
        <span class="mb-1 text-gray-600">
            ${row.created_by?.manager?.name ?? "---"}
        </span>`;
                    },
                },
                {
                    targets: 9,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${
                            row.created_at ?? "--"
                        }</span>`,
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <div>
                             <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                data-kt-menu-flip="top-end">
                                <i class="fa fa-cog"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded
                                menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4"
                                data-kt-menu="true">


                                <div class="menu-item px-3">
                                    <a href="/dashboard/${prefix}/${dbTable}/${
                            row.id
                        }"
                                       class="menu-link px-3"
                                       data-kt-docs-table-filter="show_row">${__(
                                           "Show"
                                       )}</a>
                                </div>

                                ${
                                    row.id == 1
                                        ? ""
                                        :
                                        `<div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 text-danger"
                                            data-kt-docs-table-filter="delete_row"> ${__(
                                                "Delete"
                                            )}</a>
                                        </div>`
                                }
                            </div>
                        </div>`;
                    },
                },
            ],
        });

        // Re-init functions on every table re-draw
        datatable.on("draw", function () {
            initToggleToolbar();
            toggleToolbars();
            handleShowRows();
            deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
            deleteSelectedRowsWithURL({
                url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
            });
            KTMenu.createInstances();
        });

        // ✅ ربط الفلاتر الخارجية
        $("#status_filter, #priority_filter").on("change", function () {
            datatable.ajax.reload();
        });
    };

    var handleShowRows = () => {
        const showButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="edit_row"]'
        );
        showButtons.forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                let currentBtnIndex = $(showButtons).index(btn);
                let data = datatable.row(currentBtnIndex).data();
                resetFormState();
                $("[name='complaint_number']")
                    .val(data.complaint_number)
                    .prop("disabled", true);
                $("[name='customer_name']")
                    .val(data.customer_name)
                    .prop("disabled", true);
                $("[name='customer_phone']")
                    .val(data.customer_phone)
                    .prop("disabled", true);

                $("[name='address']").val(data.address).prop("disabled", true);
                $("[name='street']").val(data.street).prop("disabled", true);
                $("[name='plot_number']")
                    .val(data.plot_number)
                    .prop("disabled", true);
            });
        });
    };

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            initToggleToolbar();
            deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
            deleteSelectedRowsWithURL({
                url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
            });
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
