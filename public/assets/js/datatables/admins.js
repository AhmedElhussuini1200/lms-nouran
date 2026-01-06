"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "admins";
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
                    d.type = $("#type_filter").val();
                    d.is_blocked = $("#is_blocked_filter").val();
                },
            },
            columns: [
                { data: "id" },
                { data: "name" },
                { data: "type" },
                { data: "email" },
                // { data: "start_contract_date" },
                // { data: "end_contract_date" },
                { data: "is_blocked" },
                { data: "company.name", name: "company.name" },
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
                                ${row.name}
                                <div class="fw-semibold fs-6 text-gray-400">${row.phone}</div>
                            </div>
                        `;
                    },
                },
                {
                    targets: 2,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${__(
                            row.type
                        )}</span>`,
                },
                {
                    targets: 3,
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${row.email}</span>`,
                },
                // {
                //     targets: 4,
                //     orderable: false,
                //     render: (data, type, row) =>
                //         `<span class="mb-1 text-gray-600">${row.start_contract_date}</span>`,
                // },
                // {
                //     targets: 5,
                //     orderable: false,
                //     render: (data, type, row) =>
                //         `<span class="mb-1 text-gray-600">${row.end_contract_date}</span>`,
                // },
                {
                    targets: 4,
                    className: "text-center",
                    render: (data, type, row) =>
                        row.is_blocked
                            ? `<span class="badge badge-light-danger">${__(
                                  "Blocked"
                              )}</span>`
                            : `<span class="badge badge-light-success">${__(
                                  "Active"
                              )}</span>`,
                },
                {
                    targets: 5,
                    className: "text-center",
                    orderable: false,
                    render: (data, type, row) =>
                        `<span class="mb-1 text-gray-600">${__(
                            data ?? "--"
                        )}</span>`,
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
        <div>
            <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
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
                        menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">

                <!-- Show -->

                <!-- Edit -->
                <div class="menu-item px-3">
                    <a href="/dashboard/${prefix}/${dbTable}/${data.id}/edit"
                       class="menu-link px-3 edit_button">
                        ${__("Edit")}
                    </a>
                </div>

                <!-- Active / InActive -->
                ${
                    data.is_blocked === 1
                        ? `<div class="menu-item px-3">
                            <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="is_blocked_row">
                                ${__("Active")}
                            </a>
                        </div>`
                        : `<div class="menu-item px-3">
                            <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="is_blocked_row">
                                ${__("InActive")}
                            </a>
                        </div>`
                }

                <!-- Delete -->
                ${
                    row.id == 1
                        ? ""
                        : `
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                            ${__("Delete")}
                        </a>
                    </div>`
                }
            </div>
        </div>
        `;
                    },
                },
            ],
        });

        $("#type_filter, #is_blocked_filter").on("change", function () {
            datatable.ajax.reload();
        });

        datatable.on("draw", function () {
            initToggleToolbar();
            toggleToolbars();
            handleShowRows();
            handleBlockingRows();
            deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
            deleteSelectedRowsWithURL({
                url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
            });
            KTMenu.createInstances();
        });
    };

    var handleShowRows = () => {
        const showButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="show_row"]'
        );

        showButtons.forEach((btn) => {
            btn.addEventListener("click", function (e) {
                let currentBtnIndex = $(showButtons).index(btn);
                let data = datatable.row(currentBtnIndex).data();

                resetFormState();

                $("#form_title").text(__("Show Admin"));
                // $("#stamp_section .image-input-wrapper").css(
                //     "background-image",
                //     `url('${data.full_image_stamp_path}')`
                // );

                // $("#signature_section .image-input-wrapper").css(
                //     "background-image",
                //     `url('${data.full_image_signature_path}')`
                // );

                  $('.image-input-wrapper').css('background-image', `url('${data.full_image_stamp_path}')`);
                $('.image-input-wrapper').css('background-position', `center`);



                  $('.image-input-wrapper').css('background-image', `url('${data.full_image_signature_path}')`);
                $('.image-input-wrapper').css('background-position', `center`);

                $("#name_inp").val(data.name).prop("disabled", true);
                $("#email_inp").val(data.email).prop("disabled", true);
                $("#phone_inp").val(data.phone).prop("disabled", true);
                $("#sector_id_inp").val(data.sector_id).prop("disabled", true);
                $("#is_blocked")
                    .prop("checked", data.is_blocked)
                    .prop("disabled", true);
                $("#city_id_inp")
                    .prop("checked", data.city_id_inp)
                    .prop("disabled", true);
                $("#company_id").val(data.company_id).prop("disabled", true);
                $("#start_contract_date_inp")
                    .val(data.start_contract_date)
                    .prop("disabled", true);
                $("#end_contract_date_inp")
                    .val(data.end_contract_date)
                    .prop("disabled", true);
                $("#type").val(data.type).prop("disabled", true);

                $("#roles_inp").val("").trigger("change");
                $.each(data.roles, function (index, role) {
                    $(`#roles_inp`)
                        .val(role.id)
                        .attr("selected", true)
                        .trigger("change");
                });
                $("#roles_inp").prop("disabled", true);

                $("#crud_form").attr("action", "#");
                $("#crud_form input[name='_method']").remove();

                $("#crud_modal").modal("show");
            });
        });
    };

    function resetFormState() {
        $("#crud_form input, #crud_form select, #crud_form textarea").prop(
            "disabled",
            false
        );
        $("#crud_form input[name='_method']").remove();
        $("#crud_form").attr("action", "#");
    }

    $("#crud_modal").on("hidden.bs.modal", function () {
        resetFormState();
    });

    var handleBlockingRows = () => {
        const blockingButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="is_blocked_row"]'
        );

        blockingButtons.forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();

                let currentBtnIndex = $(blockingButtons).index(btn);
                let data = datatable.row(currentBtnIndex).data();

                // تحديد الحالة الجديدة بعد التبديل
                let newStatus = data.is_blocked == 1 ? 0 : 1;

                $.ajax({
                    type: "patch",
                    url: `/dashboard/${prefix}/${dbTable}/status/${data.id}`,

                    data: { is_blocked: newStatus },
                    success: function () {
                        datatable.draw();
                        successAlert(
                            newStatus == 1
                                ? `${__("InActive successfully")}`
                                : `${__("Active successfully")}`
                        );
                    },
                    error: function (err) {
                        if (err.responseJSON?.message) {
                            errorAlert(err.responseJSON.message);
                        }
                        console.log(err);
                    },
                });
            });
        });
    };

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

KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
