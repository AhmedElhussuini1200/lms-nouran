"use strict";

var datatable;

// Class definition
var KTDatatablesTrash = (function () {
    let prefix = "admin"; // user type prefix for URLs
    let modelName = "Mission"; // default model
    let tabs = $(".trashTab");
    let canDelete = userAbilities.includes("delete_recycle_bin");
    let canRestore = userAbilities.includes("restore_recycle_bin");
    let lightboxPath =
        "/dashboard-assets/plugins/custom/fslightbox/fslightbox.bundle.js";

    // Table header columns for each model
    let tableHeaderColumns = new Map();
    tableHeaderColumns.set(
        "Mission",
        `<th>#</th><th>Name</th><th>Status</th><th class="min-w-100px">Actions</th>`
    );
    tableHeaderColumns.set(
        "Extinguisher",
        `<th>#</th><th>Name</th><th>Status</th><th class="min-w-100px">Actions</th>`
    );
    tableHeaderColumns.set(
        "Admin",
        `<th>#</th><th>Name</th><th>Role</th><th class="min-w-100px">Actions</th>`
    );

    // Private function: initialize DataTable
    var initDatatable = function () {
        datatable = $("#kt_datatable").DataTable({
            destroy: true,
            orderable: false,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: `/dashboard/${prefix}/trash/${modelName}?per_page=10`,
                type: "GET",
            },
            columns: dataTableColumns.get(modelName),
            columnDefs: dataTableColumnsDefs.get(modelName),
        });

        datatable.on("draw", function () {
            handleDeleteRows();
            handleRestoreRows();
            KTMenu.createInstances();
        });
    };

    // Private function: delete a row
    var handleDeleteRows = function () {
        $(".delete-row")
            .off("click")
            .on("click", function () {
                let rowId = $(this).data("row-id");
                let type = $(this).data("type");

                deleteAlert(type).then(function (result) {
                    if (result.value) {
                        loadingAlert("Deleting now...");
                        $.ajax({
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                            url: `/dashboard/${prefix}/trash/${type}/${rowId}`,
                            success: () => {
                                setTimeout(() => {
                                    successAlert(
                                        `You have deleted the ${type} successfully!`
                                    ).then(function () {
                                        datatable.draw();
                                    });
                                }, 1000);
                            },
                            error: (err) => {
                                if (
                                    err.responseJSON &&
                                    err.responseJSON.message
                                ) {
                                    errorAlert(err.responseJSON.message);
                                }
                            },
                        });
                    } else if (result.dismiss === "cancel") {
                        errorAlert("Was not deleted!");
                    }
                });
            });
    };

    // Private function: restore a row
    var handleRestoreRows = function () {
        $(".restore-row")
            .off("click")
            .on("click", function () {
                let rowId = $(this).data("row-id");
                let type = $(this).data("type");

                $.ajax({
                    method: "GET",
                    url: `/dashboard/${prefix}/trash/${type}/${rowId}/restore`,
                    success: () => {
                        setTimeout(() => {
                            successAlert(
                                `You have restored the ${type} successfully!`
                            ).then(function () {
                                datatable.draw();
                            });
                        }, 1000);
                    },
                    error: (err) => {
                        if (err.responseJSON && err.responseJSON.message) {
                            errorAlert(err.responseJSON.message);
                        }
                    },
                });
            });
    };

    // Public methods
    return {
        init: function () {
            initDatatable();

            // Handle tab clicks
            tabs.click(function () {
                tabs.removeClass("activeTab");
                $(this).addClass("activeTab");

                modelName = $(this).data("model");

                $("#table-container").html(`
                    <table id="kt_datatable" class="table text-center table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                ${tableHeaderColumns.get(modelName)}
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold text-center"></tbody>
                    </table>
                `);

                initDatatable();
            });
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesTrash.init();
});
