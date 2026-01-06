"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "mission_logs";
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
            },

            columns: [

                { data: "from_user_id", name: "from_user" },
                { data: "to_user", name: "to_user" },
                { data: "status", name: "status" },
                { data: "notes", name: "notes" },
                { data: "created_at", name: "created_at" },
            ],
            columnDefs: [
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
                        }/edit"
                                      class="menu-link px-3">${__("Edit")}</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="/dashboard/${prefix}/${dbTable}/${
                            row.id
                        }"
                                       class="menu-link px-3"
                                       data-kt-docs-table-filter="show_row">${__(
                                           "Show"
                                       )}</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-success"
                                       data-kt-docs-table-filter="approve_row"
                                       data-id="${row.id}">
                                       ${__("Approve")}
                                    </a>
                                </div>
                                ${
                                    row.id == 1
                                        ? ""
                                        : `<div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 text-danger"
                                            data-kt-docs-table-filter="delete_row">${__(
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
            handleApproveRow();
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
                $("[name='title']").val(data.title).prop("disabled", true);
                $("[name='description']")
                    .val(data.description)
                    .prop("disabled", true);
                $("[name='address']").val(data.address).prop("disabled", true);
                $("[name='street']").val(data.street).prop("disabled", true);
                $("[name='plot_number']")
                    .val(data.plot_number)
                    .prop("disabled", true);
            });
        });
    };

    // ✅ اعتماد المهمة (Approve)
    var handleApproveRow = () => {
        $(document).on(
            "click",
            '[data-kt-docs-table-filter="approve_row"]',
            function (e) {
                e.preventDefault();
                let id = $(this).data("id");
                let csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                Swal.fire({
                    title: __("Are you sure?"),
                    text: __("You want to approve this mission"),
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: __("Yes, approve it!"),
                    cancelButtonText: __("Cancel"),
                    customClass: {
                        confirmButton: "btn btn-primary", // green button
                        cancelButton: "btn btn-danger", // red button
                    },
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dashboard/${prefix}/${dbTable}/${id}/approve`,
                            type: "POST",
                            data: {
                                _token: csrfToken,
                            },
                            success: function (response) {
                                toastr.success(response.message);
                                datatable.ajax.reload(null, false);
                            },
                            error: function (xhr) {
                                toastr.error(
                                    xhr.responseJSON?.message ??
                                        "__('Error occurred')"
                                );
                            },
                        });
                    }
                });
            }
        );
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
