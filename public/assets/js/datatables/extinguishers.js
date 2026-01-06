"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "extinguishers";
    let prefix = userType;

    // Private functions
    var initDatatable = function () {
        datatable = $("#kt_datatable").DataTable({
            language: language,
            searchDelay: searchDelay,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            select:
                prefix === "admin" || prefix === "consultant"
                    ? {
                          style: "multi",
                          selector: 'td:first-child input[type="checkbox"]',
                          className: "row-selected",
                      }
                    : false,

            ajax: {
                url: `/dashboard/${prefix}/${dbTable}`,
                data: function (d) {
                    d.start_date = $("#dashboard_start_date").val();
                    d.end_date = $("#dashboard_end_date").val();
                },
            },
            columns: [
                { data: "id" },
                { data: "address" },
                { data: "street" },
                { data: "feeder" },
                { data: "station_id" },
                { data: "company_id" },
                { data: "created_at" },
                { data: "status_id" },
                { data: "complaint_type_id" },
                // { data: "extinguish_type_id" },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        if (prefix === "admin" || prefix === "consultant") {
                            return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid" style="justify-content: center;">
                                <input class="form-check-input" type="checkbox" value="${data}" />
                            </div>`;
                        } else {
                            return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid" style="justify-content: center;">
                                <span class="mb-1 text-gray-600">${
                                    meta.row + 1
                                }</span>
                            </div>`;
                        }
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.address}</a>
                                </div>`;
                    },
                },

                {
                    targets: 2,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.street}</a>
                                </div>`;
                    },
                },

                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.feeder}</a>
                                </div>`;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">
                                        ${__(
                                            row.station
                                                ? row.station.name_ar
                                                : "--"
                                        )}
                                    </a>
                                </div>`;
                    },
                },

                {
                    targets: 5,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">



                                        ${__(
                                            row.company
                                                ? row.company.name
                                                : "--"
                                        )}

                                    </a>
                                </div>`;
                    },
                },

                {
                    targets: 6,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column justify-content-center">
                                    <a href="javascript:;" class="mb-1 text-gray-800 text-hover-primary">${row.created_at}</a>
                                </div>`;
                    },
                },
                {
                    targets: 7,
                    orderable: false,
                    render: (data, type, row) => {
                        if (!row.status) return "--";
                        return `
                            <span class="badge"
                                style="background-color:${
                                    row.status.color ?? "#ccc"
                                }; color:#fff;">
                                ${__(row.status.name)}
                            </span>`;
                    },
                },
                {
                    targets: 8,
                    orderable: false,
                    render: (data, type, row) => {
                        if (!row.complainttype) return "--";
                        return `
                            <span class="badge"
                                style="background-color:${
                                    row.complainttype.color ?? "#ccc"
                                }; color:#fff;">
                                ${row.complainttype.name_ar}
                            </span>`;
                    },
                },
                // {
                //     targets: 9,
                //     orderable: false,
                //     render: (data, type, row) => {
                //         if (!row.extinguishingType) return "--";
                //         return `
                //             <span class="badge"
                //                 style="background-color:${
                //                     row.extinguishingType.color ?? "#ccc"
                //                 }; color:#fff;">
                //                 ${row.extinguishingType.name}
                //             </span>`;
                //     },
                // },

                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        const showLink = `
            <div class="menu-item px-3">
                <a href="/dashboard/${prefix}/${dbTable}/${data.id}"
                class="menu-link px-3 data-kt-docs-table-filter="show_row"
                                " >
                                                    ${__("Show")}
                                                </a>
                                            </div>
                                        `;

                        let adminActions = "";

                        if (prefix === "admin") {
                            adminActions = `
                <div class="menu-item px-3">
                    <a href="javascript:;" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                        ${__("Edit")}
                    </a>
                </div>

                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                        ${__("Delete")}
                    </a>
                </div>


            `;
                        }

                        return `
        <div>
            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                <span class="svg-icon svg-icon-dark svg-icon-1 m-0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="currentColor"/>
                    <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="currentColor"/>
                    </svg>
                </span>
            </a>

            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                ${showLink}
                ${adminActions}
            </div>
        </div>`;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                // optional: add attributes
            },
        });
        $("#dashboard_start_date, #dashboard_end_date").on(
            "change",
            function () {
                datatable.ajax.reload();
            }
        );

        datatable.on("draw", function () {
            initToggleToolbar();
            if (prefix === "admin" || prefix === "consultant") {
                handleEditRows();
                toggleToolbars();

                deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
                deleteSelectedRowsWithURL({
                    url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                    restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
                });
            }
            $("#filter_date_btn").on("click", function () {
                datatable.ajax.reload();
            });

            KTMenu.createInstances();
        });
    };

    var handleEditRows = () => {
        const editButtons = document.querySelectorAll(
            '[data-kt-docs-table-filter="edit_row"]'
        );

        editButtons.forEach((d) => {
            d.addEventListener("click", function (e) {
                e.preventDefault();
                let currentBtnIndex = $(editButtons).index(d);
                let data = datatable.row(currentBtnIndex).data();

                $("#form_title").text(__("Edit Extinguisher"));

                $("#report_number_inp")
                    .val(data.report_number)
                    .trigger("change");
                $("#city_id_inp").val(data.city_id).trigger("change");

                $("#municipality_id_inp")
                    .val(data.municipality_id)
                    .trigger("change");

                $("#district_id_inp").val(data.district_id).trigger("change");

                $("#neighborhood_id_inp")
                    .val(data.neighborhood_id)
                    .trigger("change");

                $("#sector_id_inp").val(data.sector_id).trigger("change");
                $("#station_id_inp").val(data.station_id).trigger("change");

                $("#light_columns_id_inp")
                    .val(data.light_columns_id)
                    .trigger("change");
                $("#street_inp").val(data.street).trigger("change");
                $("#address_inp").val(data.address).trigger("change");
                $("#priority_id_inp").val(data.priority_id).trigger("change");
                $("#company_id_inp").val(data.company_id).trigger("change");

                $("#feeder_inp").val(data.feeder).trigger("change");

                $("#contract_id_inp").val(data.contract_id).trigger("change");

                // $("#feeder_inp").val(data.feeder).trigger("change");
                // $("#station_id_inp").val(data.station_id).trigger("change");
                // $("#action_id_inp").val(data.action_id).trigger("change");

                $("#complaint_type_id_inp")
                    .val(data.complaint_type_id)
                    .trigger("change");
                $("#extinguish_type_id_inp")
                    .val(data.extinguish_type_id)
                    .trigger("change");

                $("#kt_datepicker_1_inp")
                    .val(data.report_time)
                    .trigger("change");
                $("#response_duration_inp")
                    .val(data.response_duration)
                    .trigger("change");

                $("#description_inp").val(data.description).trigger("change");
                $("#crud_form").attr(
                    "action",
                    `/dashboard/${prefix}/${dbTable}/${data.id}`
                );
                $("#crud_form").prepend(
                    `<input type="hidden" name="_method" value="PUT">`
                );
                $("#crud_modal").modal("show");
                // console.log(data);
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
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            if (prefix === "admin" || prefix === "consulant") {
                initToggleToolbar();
                handleEditRows();
                deleteRowWithURL(`/dashboard/${prefix}/${dbTable}/`);
                deleteSelectedRowsWithURL({
                    url: `/dashboard/${prefix}/${dbTable}/delete-selected`,
                    restoreUrl: `/dashboard/${prefix}/${dbTable}/restore-selected`,
                });
            }
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
