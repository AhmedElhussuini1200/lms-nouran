"use strict";

var datatable;

// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "contractItems";
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
                    d.contract_id = $("#type_filter").val();
                    d.status_id = $("#status_filter").val();
                    d.contractor_id = $("#contractor_filter").val();
                },
            },
            columns: [
                { data: "id" },
                { data: "item_description" },
                { data: "contract_id" },
                { data: "unit_id" },
                { data: "quantity" },
                { data: "unit_price" },
                { data: "unit_price_text" },
                { data: "total_price" },
                { data: "total_unit_price_text" },
                { data: "status" },
                { data: "created_by" }, //12
                { data: "approved_by" }, //11
                // { data:"is_completed" },//12
                { data: "created_at" }, //13
                { data: "description" },
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
                    targets: 2,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${__(
                                        row.contract.name_ar
                                    )}</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.unit.name}</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: 9,
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
                    targets: 11,
                    render: function (data, type, row) {
                        if (row.is_approved) {
                            return `
                                <div class="d-flex flex-column justify-content-center">
                                    <span class="text-gray-800 fw-bold">
                                        ${row.approved?.name ?? "--"}
                                    </span>
                                </div>`;
                        }
                        return `
                            <div class="d-flex flex-column justify-content-center">
                                <span class="b-1 text-gray-800">${__(
                                    "Not approved"
                                )}</span>
                            </div>`;
                    },
                },
                {
                    targets: 10,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800">${
                                        row.created_by.name ?? "--"
                                    }</a>
                                </div>
                            </div>`;
                    },
                },

                {
                    targets: 12,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800">${
                                        row.created_at ?? "--"
                                    }</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: 13,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800">${
                                        row.description ?? "--"
                                    }</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        let editHtml = "";
                        if (
                            prefix === "admin" ||
                            (prefix === "contractor" && !row.is_approved)
                        ) {
                            editHtml = `
                <div class="menu-item px-3">
                    <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                        ${__("Edit")}
                    </a>
                </div>`;
                        }

                        let showHtml = `
            <div class="menu-item px-3">
                <a href="/dashboard/${prefix}/${dbTable}/${row.id}"
                   class="menu-link px-3 show_button">
                    ${__("Show")}
                </a>
            </div>`;

                        let approveHtml = "";
                        if (
                            !row.is_approved &&
                            (prefix === "admin" || prefix === "amana")
                        ) {
                            approveHtml = `
                <div class="menu-item px-3">
                    <a href="javascript:;" class="menu-link px-3 approve-btn" data-id="${
                        row.id
                    }">
                        ${__("Approve")}
                    </a>
                </div>`;
                        }

                        let completedHtml = "";
                        if (
                            row.createdBy &&
                            row.createdBy.reporting_to_id === currentUserId
                        ) {
                            completedHtml = `
                <div class="menu-item px-3">
                    <label class="form-check form-check-inline">
                        <input type="checkbox" class="is-completed-checkbox" data-id="${
                            row.id
                        }"
                            ${row.is_completed ? "checked disabled" : ""}>
                        <span class="ms-2">
                            ${
                                row.is_completed
                                    ? __("Completed")
                                    : __("Mark as completed")
                            }
                        </span>
                    </label>
                </div>`;
                        }

                        return `
            <div>
                <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                   data-kt-menu-trigger="click"
                   data-kt-menu-placement="bottom-end"
                   data-kt-menu-flip="top-end">
                    <span class="svg-icon svg-icon-dark svg-icon-1 m-0">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3"
                                  d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z"
                                  fill="currentColor"/>
                            <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z"
                                  fill="currentColor"/>
                        </svg>
                    </span>
                </a>

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600
                            menu-state-bg-light-primary fw-bold fs-7 w-200px py-4"
                     data-kt-menu="true">

                     ${editHtml}
                     ${showHtml}
                     ${approveHtml}
                     ${completedHtml}

                     <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3 delete-row" data-id="${
                            row.id
                        }">
                            ${__("Delete")}
                        </a>
                     </div>

                </div>
            </div>`;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {},
        });

        $("#type_filter, #status_filter").on("change", function () {
            datatable.ajax.reload();
        });
        $("#contractor_filter").on("change", function () {
            datatable.ajax.reload();
        });

        // Re-init functions on every table re-draw
        datatable.on("draw", function () {
            initToggleToolbar();
            toggleToolbars();
            handleEditRows();
            deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
            deleteSelectedRowsWithURL({
                url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
            });
            $("#crud_form")[0].reset();
            KTMenu.createInstances();
        });

        // ✅ زر الاعتماد
        $("#kt_datatable").on("click", ".approve-btn", function () {
            const button = $(this);
            const itemId = button.data("id");
            button.prop("disabled", true).text("Approving...");

            $.ajax({
                url: `/dashboard/${prefix}/${dbTable}/${itemId}/approve`,
                type: "POST",
                data: { _token: $('meta[name="csrf-token"]').attr("content") },
                success: function (res) {
                    Swal.fire({
                        icon: "success",
                        title: __("Done successfully"),
                        text: __("contractItem approved successfully"),
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    // تحديث الجدول بدون عمل Refresh كامل للصفحة
                    $("#kt_datatable").DataTable().ajax.reload(null, false); // false = الحفاظ على الصفحة الحالية
                },
                error: function (err) {
                    Swal.fire({
                        icon: "error",
                        title: __("Error !"),
                        text: __("Something went wrong"),
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    button.prop("disabled", false).text("Approve");
                },
            });
        });

        // ✅ AJAX لتحديث is_completed
        // $("#kt_datatable").on("click", ".approve-btn", function () {
        //     const button = $(this);
        //     const itemId = button.data("id");
        //     button.prop("disabled", true).text("Approving...");

        //     $.ajax({
        //         url: `/dashboard/${prefix}/${dbTable}/${itemId}/approve`,
        //         type: "POST",
        //         data: { _token: $('meta[name="csrf-token"]').attr("content") },
        //         success: function (res) {
        //             Swal.fire({
        //                 icon: "success",
        //                 title: __("Done successfully!"),
        //                 text: __("contractItem approved successfully "),
        //                 timer: 2000,
        //                 showConfirmButton: false,
        //             });
        //         },
        //         error: function (err) {
        //             Swal.fire({
        //                 icon: "error",
        //                 title: __("Error!"),
        //                 text: __("Something went wrong"),
        //                 timer: 2000,
        //                 showConfirmButton: false,
        //             });
        //             button.prop("disabled", false).text__("Approve");
        //         },
        //     });
        // });
    };

    var handleEditRows = () => {
        const editButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="edit_row"]'
        );

        editButtons.forEach((d) => {
            d.addEventListener("click", function (e) {
                e.preventDefault();
                let currentBtnIndex = $(editButtons).index(d);
                let rowData = datatable.row(currentBtnIndex).data();
                let itemId = rowData.id;

                $("#form_title").text(__("Edit contract Item"));
                $("#add_row_btn").hide();
                $("#items_table .delete-row").hide();
                $("#items_count_inp").closest(".fv-row").hide();

                const tbody = $("#items_table tbody");
                tbody.empty();
                $("#contract_id_inp")
                    .val(rowData.contract_id)
                    .trigger("change");

                let units = window.units || [];
                let unitsSelect = "";
                units.forEach((unit) => {
                    unitsSelect += `
                        <option value="${unit.id}" ${
                        unit.id == rowData.unit.id ? "selected" : ""
                    }>
                            ${unit.name}
                        </option>`;
                });

                tbody.append(`
<tr>
    <td class="row-index">1</td>
    <td><input type="text" name="item_description[]" class="form-control" value="${
        rowData.item_description ?? ""
    }"></td>

        <td><input type="text" name="description" class="form-control" value="${
            rowData.description ?? ""
        }"></td>
    <td>
        <select name="unit_id[]" class="form-select form-select-lg form-select-solid"
                data-control="select2" data-dir="${isArabic ? "rtl" : "ltr"}">
            <option value=""></option>
            ${unitsSelect}
        </select>
    </td>
    <td><input type="number" name="quantity" class="form-control" value="${
        rowData.quantity ?? 1
    }"></td>
    <td><input type="text" name="unit_price" class="form-control" value="${Number(
        rowData.unit_price ?? 0
    ).toLocaleString()}"></td>
    <td><input type="text" name="unit_price_text" class="form-control" value="${
        rowData.unit_price_text ?? ""
    }"></td>
    <td><input type="text" name="total_price" class="form-control" value="${Number(
        rowData.total_price ?? 0
    ).toLocaleString()}"></td>
    <td><input type="text" name="total_unit_price_text" class="form-control" value="${
        rowData.total_unit_price_text ?? ""
    }"></td>
    <td></td>
</tr>`);

                $("#crud_form").attr(
                    "action",
                    `/dashboard/${prefix}/${dbTable}/${itemId}`
                );
                $("#crud_form").prepend(
                    `<input type="hidden" name="_method" value="PUT">`
                );
                $("#crud_modal").modal("show");
                $('select[data-control="select2"]').select2();
            });
        });

        $("#add_btn").click(function () {
            $("#add_row_btn").show();
            $("#items_table .delete-row").show();
            $("#items_count_inp").closest(".fv-row").show();
        });
    };
    $(document).on("click", ".delete-row", function (e) {
        e.preventDefault();
        let rowId = $(this).data("id");
        let rowData = datatable.row($(this).closest("tr")).data(); // استخدم datatable وليس table

        if (rowData.is_approved) {
            // رسالة Metronic
            toastr.error(
                "This item cannot be deleted because it has been approved by the manager"
            );
            return false;
        }

        // كود الحذف الفعلي
        deleteItem(rowId);
    });

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            initToggleToolbar();
            handleEditRows();
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
