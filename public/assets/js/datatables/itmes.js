"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "items";
    let prefix = "admin";

    // Private functions
    var initDatatable = function () {
        datatable = $("#kt_datatable").DataTable({
            language: language,
            searchDelay: searchDelay,
            // processing: processing,
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
                //      data: function (d) {
                // d.city_id = $('#type_filter').val();
                //     }
            },
            columns: [
                { data: "id" },
                { data: "title" },
                { data: "description" },
                // { data: "quantity" },
                // { data: "status_id" },
                { data: "unit_id" },
                { data: "warehouse_id" },
                // { data: "created_by" },
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
                    render: function (data, type, row) {
                        return `
                            <div>
                                <!--begin::Info-->
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.title}</a>
                                </div>
                                <!--end::Info-->
                            </div>
                        `;
                    },
                },

                {
                    targets: 2,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <!--begin::Info-->
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${
                                        row.description ?? "--"
                                    }</a>
                                </div>
                                <!--end::Info-->
                            </div>
                        `;
                    },
                },

                // {
                //     targets: 3,
                //     render: function (data, type, row) {
                //         return `
                //             <div>
                //                 <!--begin::Info-->
                //                 <div class="d-flex flex-column justify-content-center">
                //                     <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${
                //                         row.quantity ?? "--"
                //                     }</a>
                //                 </div>
                //                 <!--end::Info-->
                //             </div>
                //         `;
                //     },
                // },
                // {
                //     targets: 3,
                //     orderable: false,
                //     render: (data, type, row) => {
                //         if (!row.status) return "--";
                //         return `
                //             <span class="badge"
                //                 style="background-color:${
                //                     row.status.color ?? "#ccc"
                //                 }; color:#fff;">
                //                 ${row.status.name}
                //             </span>`;
                //     },
                // },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800">${__(
                                        row.unit.name ?? "--"
                                    )}</a>
                                </div>
                            </div>`;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return `
                            <div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800">${__(
                                        row.warehouse.name ?? "--"
                                    )}</a>
                                </div>
                            </div>`;
                    },
                },
                // {
                //     targets: 6,
                //     render: function (data, type, row) {
                //         return `
                //             <div>
                //                 <div class="d-flex flex-column justify-content-center">
                //                     <a href="javascript:;" class="mb-1 text-gray-800">${__(
                //                         row.createdBy.name ?? "--"
                //                     )}</a>
                //                 </div>
                //             </div>`;
                //     },
                // },

                {
                    targets: 5,
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

                //         {
                //             targets: -1,
                //             data: null,
                //             orderable: false,
                //             render: function (data, type, row) {
                //                 return `
                // <div>
                //    <a href="#" class="btn btn-light btn-active-light-primary btn-sm " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                //                        <span class="svg-icon svg-icon-dark svg-icon-1 m-0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                //                            <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="currentColor"/>
                //                            <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="currentColor"/>
                //                             </svg>
                //                        </span>
                //                     </a>
                //     <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">

                //         <div class="menu-item px-3">
                //             <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                //                 ${__("Edit")}
                //             </a>
                //         </div>

                //         <div class="menu-item px-3">
                //             <a href="javascript:;" class="menu-link px-3 text-success" data-kt-docs-table-filter="approve_row" data-id="${
                //                 row.id
                //             }">
                //                 ${__("Approve")}
                //             </a>
                //         </div>

                //              <div class="menu-item px-3">
                //             <a href="javascript:;" class="menu-link px-3 text-success" data-kt-docs-table-filter="reject_row" data-id="${
                //                 row.id
                //             }">
                //                 ${__("DisApprove")}
                //             </a>
                //         </div>

                //         <div class="menu-item px-3">
                //             <a href="#" class="menu-link px-3 text-danger" data-kt-docs-table-filter="delete_row">
                //                 ${__("Delete")}
                //             </a>
                //         </div>
                //     </div>
                // </div>`;
                //             },
                //         },

                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        let approveRejectBtns = "";
                        // فقط مدير المستودع يمكنه الموافقة أو الرفض
                        if (
                            row.warehouse.manager_id === currentUser.id &&
                            !row.is_approved &&
                            row.status.name_en !== "rejected"
                        ) {
                            approveRejectBtns = `
        <div class="menu-item px-3">
            <a href="javascript:;" class="menu-link px-3 text-success" data-kt-docs-table-filter="approve_row" data-id="${
                row.id
            }">
                ${__("Approve")}
            </a>
        </div>
        <div class="menu-item px-3">
            <a href="javascript:;" class="menu-link px-3 text-warning" data-kt-docs-table-filter="reject_row" data-id="${
                row.id
            }">
                ${__("DisApprove")}
            </a>
        </div>
    `;
                        }

                        return `
        <div>
           <a href="#" class="btn btn-light btn-active-light-primary btn-sm " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                               <span class="svg-icon svg-icon-dark svg-icon-1 m-0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                   <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="currentColor"/>
                                   <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="currentColor"/>
                                    </svg>
                               </span>
                            </a>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                        ${__("Edit")}
                    </a>
                </div>

                ${approveRejectBtns}

                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 text-danger" data-kt-docs-table-filter="delete_row">
                        ${__("Delete")}
                    </a>
                </div>
            </div>
        </div>`;
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
            initToggleToolbar();
            toggleToolbars();
            handleEditRows();
            handleApproveRow();
            handleRejectRow();
            deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
            deleteSelectedRowsWithURL({
                url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
            });
            KTMenu.createInstances();
        });
    };

    var handleEditRows = () => {
        // Select all edit buttons
        const editButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="edit_row"]'
        );

        editButtons.forEach((d) => {
            // edit button on click
            d.addEventListener("click", function (e) {
                e.preventDefault();

                let currentBtnIndex = $(editButtons).index(d);
                let data = datatable.row(currentBtnIndex).data();

                $("#form_title").text(__("Edit Items"));
                $("#title_inp").val(data.title);
                $("#description_inp").val(data.description);
                $("#quantity_inp").val(data.quantity);
                // $("#description_inp").val(data.description);
                $("#unit_id_inp").val(data.unit_id).trigger("change");
                $("#warehouse_id_inp").val(data.warehouse_id).trigger("change");

                $("#crud_form").attr(
                    "action",
                    `/dashboard/${prefix}/${dbTable}/${data.id}`
                );
                $("#crud_form").prepend(
                    `<input type="hidden" name="_method" value="PUT">`
                );
                $("#crud_modal").modal("show");
            });
        });
    };

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
                    text: __("You want to approve this items"),
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
    var handleRejectRow = () => {
        $(document).on(
            "click",
            '[data-kt-docs-table-filter="reject_row"]',
            function (e) {
                e.preventDefault();
                let id = $(this).data("id");
                let csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");

                Swal.fire({
                    title: __("Are you sure?"),
                    text: __("You want to reject this item"),
                    icon: "warning",
                    input: "text", // لإدخال سبب الرفض
                    inputPlaceholder: __("Enter rejection reason"),
                    showCancelButton: true,
                    confirmButtonText: __("Yes, reject it!"),
                    cancelButtonText: __("Cancel"),
                    customClass: {
                        confirmButton: "btn btn-danger", // أحمر للرفض
                        cancelButton: "btn btn-secondary",
                    },
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage(
                                __("Rejection reason is required")
                            );
                        }
                        return reason;
                    },
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dashboard/${prefix}/${dbTable}/${id}/reject`,
                            type: "POST",
                            data: {
                                _token: csrfToken,
                                rejected_reason: result.value, // نرسل سبب الرفض
                            },
                            success: function (response) {
                                toastr.success(response.message);
                                datatable.ajax.reload(null, false);
                            },
                            error: function (xhr) {
                                toastr.error(
                                    xhr.responseJSON?.message ??
                                        __("Error occurred")
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
            handleApproveRow();
            handleRejectRow();
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
