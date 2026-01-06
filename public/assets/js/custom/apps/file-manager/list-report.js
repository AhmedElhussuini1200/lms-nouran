"use strict";

// const { createLogger } = require("vite");

// const { data } = require("autoprefixer");

// Class definition
var KTFileManagerList = function () {
    // Define shared variables
    var datatable;
    var table

    // Define template element variables
    var uploadTemplate;
    var renameTemplate;
    var actionTemplate;
    var checkboxTemplate;


    // Private functions
    const initTemplates = () => {
        uploadTemplate = document.querySelector('[data-kt-filemanager-template="upload"]');
        renameTemplate = document.querySelector('[data-kt-filemanager-template="rename"]');
        actionTemplate = document.querySelector('[data-kt-filemanager-template="action"]');
        checkboxTemplate = document.querySelector('[data-kt-filemanager-template="checkbox"]');
    }

    const initDatatable = () => {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const dateCol = dateRow[3]; // select date from 4th column in table
            // const realDate = moment(dateCol.innerHTML, "DD MMM YYYY, LT").format();
            // dateCol.setAttribute('data-order', realDate);
        });

        const foldersListOptions = {
            "info": false,
            'order': [],
            "scrollY": "700px",
            "scrollCollapse": true,
            "paging": false,
            'ordering': false,
            'columns': [
                { data: 'checkbox' },
                { data: 'name' },
                // { data: 'size' },
                // { data: 'date' },
                { data: 'action' },
            ],
            'language': {
                emptyTable: `<div class="d-flex flex-column flex-center">
                    <img src="${hostUrl}media/illustrations/sketchy-1/5.png" class="mw-400px" />
                    <div class="fs-1 fw-bolder text-dark">No items found.</div>
                    <div class="fs-6">Start creating new folders or uploading a new file!</div>
                </div>`
            }
        };

        const filesListOptions = {
            "info": false,
            'order': [],
            'pageLength': 10,
            "lengthChange": false,
            'ordering': false,
            'columns': [
                { data: 'checkbox' },
                { data: 'name' },
                { data: 'created_by' },
                { data: 'size' },
                { data: 'status' },
                { data: 'date' },
                { data: 'action' },
            ],
            'language': {
                emptyTable: `<div class="d-flex flex-column flex-center">
                    <div class="fs-6">${__("No data available in table")}</div>
                </div>`,
            },
            conditionalPaging: true
        };

        // Define datatable options to load
        var loadOptions;
        if (table.getAttribute('data-kt-filemanager-table') === 'folders') {
            loadOptions = foldersListOptions;
        } else {
            loadOptions = filesListOptions;
        }

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable(loadOptions);

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            // toggleToolbars();
            resetNewFolder();
            KTMenu.createInstances();
            initCopyLink();
            // countTotalItems();
            handleRename();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-filemanager-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Delete customer
    const handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-filemanager-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const fileName = parent.querySelectorAll('td')[1].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: __("Are you sure you want to delete ") + fileName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: __("Yes, delete!"),
                    cancelButtonText: __("No, cancel"),
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        const link = parent.querySelector('td a[href*="/dashboard/admin/reports/"]');
                        let folderId = null;
                        if (link && link.href) {
                            const match = link.href.match(/reports\/(\d+)/);
                            folderId = match ? match[1] : null;
                        }
                        if (!folderId) {
                            toastr.error(__('Unable to detect folder ID'));
                            return;
                        }
                        $.ajax({
                            url: '/dashboard/admin/reports/folder/' + folderId,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                            success: function () {
                                datatable.row($(parent)).remove().draw();
                                toastr.success(__('Folder deleted successfully'));
                            },
                            error: function (xhr) {
                                var msg = __('Error deleting folder');
                                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                toastr.error(msg);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: fileName + " " + __("was not deleted."),
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: __("Ok, got it!"),
                            customClass: { confirmButton: "btn fw-bold btn-primary" }
                        });
                    }
                });
            })
        });
    }

    // Init toggle toolbar
    const initToggleToolbar = () => {
        // Toggle selected action toolbar
        // Select all checkboxes
        var checkboxes = table.querySelectorAll('[type="checkbox"]');
        if (table.getAttribute('data-kt-filemanager-table') === 'folders') {
            checkboxes = document.querySelectorAll('#kt_file_manager_list_wrapper [type="checkbox"]');
        }

        // Select elements
        const deleteSelected = document.querySelector('[data-kt-filemanager-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                console.log(c);
                setTimeout(function () {
                    // toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        // deleteSelected.addEventListener('click', function () {
        //     // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
        //     Swal.fire({
        //         text: "Are you sure you want to delete selected files or folders?",
        //         icon: "warning",
        //         showCancelButton: true,
        //         buttonsStyling: false,
        //         confirmButtonText: "Yes, delete!",
        //         cancelButtonText: "No, cancel",
        //         customClass: {
        //             confirmButton: "btn fw-bold btn-danger",
        //             cancelButton: "btn fw-bold btn-active-light-primary"
        //         }
        //     }).then(function (result) {
        //         if (result.value) {
        //             Swal.fire({
        //                 text: "You have deleted all selected  files or folders!.",
        //                 icon: "success",
        //                 buttonsStyling: false,
        //                 confirmButtonText: "Ok, got it!",
        //                 customClass: {
        //                     confirmButton: "btn fw-bold btn-primary",
        //                 }
        //             }).then(function () {
        //                 // Remove all selected customers
        //                 checkboxes.forEach(c => {
        //                     if (c.checked) {
        //                         datatable.row($(c.closest('tbody tr'))).remove().draw();
        //                     }
        //                 });

        //                 // Remove header checked box
        //                 const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
        //                 headerCheckbox.checked = false;
        //             });
        //         } else if (result.dismiss === 'cancel') {
        //             Swal.fire({
        //                 text: "Selected  files or folders was not deleted.",
        //                 icon: "error",
        //                 buttonsStyling: false,
        //                 confirmButtonText: "Ok, got it!",
        //                 customClass: {
        //                     confirmButton: "btn fw-bold btn-primary",
        //                 }
        //             });
        //         }
        //     });
        // });
    }

    // Toggle toolbars
    // const toggleToolbars = () => {
    //     // Define variables
    //     const toolbarBase = document.querySelector('[data-kt-filemanager-table-toolbar="base"]');
    //     const toolbarSelected = document.querySelector('[data-kt-filemanager-table-toolbar="selected"]');
    //     const selectedCount = document.querySelector('[data-kt-filemanager-table-select="selected_count"]');

    //     // Select refreshed checkbox DOM elements
    //     const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

    //     // Detect checkboxes state & count
    //     let checkedState = false;
    //     let count = 0;

    //     // Count checked boxes
    //     allCheckboxes.forEach(c => {
    //         if (c.checked) {
    //             checkedState = true;
    //             count++;
    //         }
    //     });

    //     // Toggle toolbars
    //     if (checkedState) {
    //         selectedCount.innerHTML = count;
    //         toolbarBase.classList.add('d-none');
    //         toolbarSelected.classList.remove('d-none');
    //     } else {
    //         toolbarBase.classList.remove('d-none');
    //         toolbarSelected.classList.add('d-none');
    //     }
    // }

    // Handle new folder
    const handleNewFolder = () => {
        // Select button
        const newFolder = document.getElementById('kt_file_manager_new_folder');
        if (newFolder) {
            // Handle click action
            newFolder.addEventListener('click', e => {
                e.preventDefault();

                // Ignore if input already exist
                if (table.querySelector('#kt_file_manager_new_folder_row')) {
                    return;
                }

                // Add new blank row to datatable
                const tableBody = table.querySelector('tbody');
                const rowElement = uploadTemplate.cloneNode(true); // Clone template markup
                tableBody.prepend(rowElement);

                // Define template interactive elements
                const rowForm = rowElement.querySelector(
                    "#kt_file_manager_add_folder_form"
                );
                const rowButton = rowElement.querySelector('#kt_file_manager_add_folder');
                const cancelButton = rowElement.querySelector('#kt_file_manager_cancel_folder');
                const folderIcon = rowElement.querySelector('#kt_file_manager_folder_icon');
                const nameAr = rowElement.querySelector('[name="name_ar"]');
                const nameEn = rowElement.querySelector('[name="name_en"]');

                // Define validator
                // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
                var validator = FormValidation.formValidation(
                    rowForm,
                    {
                        fields: {
                            'name_en': {
                                rowSelector: '#name_en_validation',
                                validators: {
                                    notEmpty: {
                                        message: __('Folder name in English is required')
                                    }
                                }
                            },
                            'name_ar': {
                                rowSelector: '#name_ar_validation', // 👈 target Arabic div
                                validators: {
                                    notEmpty: {
                                        message: __('Folder name in Arabic is required')
                                    }
                                }
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: '.fv-row',
                                eleInvalidClass: '',
                                eleValidClass: ''
                            }),
                            message: new FormValidation.plugins.Message({
                                container: function (field) {
                                    if (field === 'name_en') return rowForm.querySelector('#name_en_validation');
                                    if (field === 'name_ar') return rowForm.querySelector('#name_ar_validation');
                                    return rowForm;
                                },
                                class: 'invalid-feedback d-block mt-2'
                            })
                        }
                    },
                );

                // Handle add new folder button
                rowButton.addEventListener('click', e => {
                    e.preventDefault();

                    // Activate indicator
                    rowButton.setAttribute("data-kt-indicator", "on");

                    // Validate form before submit
                    if (validator) {
                        validator.validate().then(function (status) {
                            if (status == 'Valid') {
                                // setTimeout(function () {
                                //     // Create folder link
                                //     const folderLink =
                                //         document.createElement("a");
                                //     const folderLinkClasses = [
                                //         "text-gray-800",
                                //         "text-hover-primary",
                                //     ];
                                //     folderLink.setAttribute(
                                //         "href",
                                //         "?page=apps/file-manager/blank"
                                //     );
                                //     folderLink.classList.add(
                                //         ...folderLinkClasses
                                //     );
                                //     folderLink.innerText = locale === 'ar' ? nameAr.value : nameEn.value;

                                //     const newRow = datatable.row
                                //         .add({
                                //             checkbox:
                                //                 1,
                                //             name:
                                //                 folderIcon.outerHTML +
                                //                 folderLink.outerHTML,
                                //             size: "-",
                                //             date: "-",
                                //             action: "actionTemplate.innerHTML",
                                //         })
                                //         .node();
                                //     $(newRow)
                                //         .find("td")
                                //         .eq(4)
                                //         .attr(
                                //             "data-kt-filemanager-table",
                                //             "action_dropdown"
                                //         );
                                //     $(newRow)
                                //         .find("td")
                                //         .eq(4)
                                //         .addClass("text-end"); // Add custom class to last 'td' element --- more info: https://datatables.net/forums/discussion/22341/row-add-cell-class

                                //     // Re-sort datatable to allow new folder added at the top
                                //     var index = datatable.row(0).index(),
                                //         rowCount = datatable.data().length - 1,
                                //         insertedRow = datatable
                                //             .row(rowCount)
                                //             .data(),
                                //         tempRow;

                                //     for (var i = rowCount; i > index; i--) {
                                //         tempRow = datatable.row(i - 1).data();
                                //         datatable.row(i).data(tempRow);
                                //         datatable.row(i - 1).data(insertedRow);
                                //     }

                                //     toastr.options = {
                                //         closeButton: true,
                                //         debug: false,
                                //         newestOnTop: false,
                                //         progressBar: false,
                                //         positionClass: "toastr-top-right",
                                //         preventDuplicates: false,
                                //         showDuration: "300",
                                //         hideDuration: "1000",
                                //         timeOut: "5000",
                                //         extendedTimeOut: "1000",
                                //         showEasing: "swing",
                                //         hideEasing: "linear",
                                //         showMethod: "fadeIn",
                                //         hideMethod: "fadeOut",
                                //     };

                                //     toastr.success(
                                //         nameEn.value + " was created!"
                                //     );

                                //     // Disable indicator
                                //     rowButton.removeAttribute(
                                //         "data-kt-indicator"
                                //     );

                                //     // Reset input
                                //     nameEn.value = "";
                                //     nameAr.value = "";

                                //     datatable.draw(false);
                                // }, 2000);


                                $.ajax({
                                    url: '/dashboard/admin/reports/folder/store',
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                    },
                                    data: {
                                        name_en: nameEn.value.trim(),
                                        name_ar: nameAr.value.trim(),
                                    },
                                    success: function (response) {
                                        toastr.options = {
                                            "closeButton": true,
                                            "debug": false,
                                            "newestOnTop": false,
                                            "progressBar": false,
                                            "positionClass": "toastr-top-right",
                                            "preventDuplicates": false,
                                            "showDuration": "300",
                                            "hideDuration": "1000",
                                            "timeOut": "5000",
                                            "extendedTimeOut": "1000",
                                            "showEasing": "swing",
                                            "hideEasing": "linear",
                                            "showMethod": "fadeIn",
                                            "hideMethod": "fadeOut"
                                        };

                                        rowButton.removeAttribute("data-kt-indicator");

                                        var isAr = (window.isArabic === '1' || window.isArabic === true || window.isArabic === 'true');
                                        var displayName = isAr ? (response.name_ar || nameAr.value.trim()) : (response.name_en || nameEn.value.trim());
                                        var linkHref = '/dashboard/admin/reports/' + (response.id || '');
                                        var nameHTML = '<div class="d-flex align-items-center">' +
                                            folderIcon.outerHTML +
                                            '<a href="' + linkHref + '" class="text-gray-800 text-hover-primary">' + displayName + '</a>' +
                                            '</div>';

                                        var actionCell = table.querySelector('tbody td[data-kt-filemanager-table="action_dropdown"]');
                                        var actionHTML = actionCell ? actionCell.innerHTML : (actionTemplate && actionTemplate.innerHTML ? actionTemplate.innerHTML : '');

                                        var checkboxHTML = '#';

                                        var addedRow = datatable.row.add({
                                            checkbox: checkboxHTML,
                                            name: nameHTML,
                                            action: actionHTML
                                        }).draw(false);

                                        var node = addedRow.node();
                                        if (node) {
                                            $(node).find('td').eq(2).attr('data-kt-filemanager-table', 'action_dropdown').addClass('text-end');
                                            var renameBtn = $(node).find('[data-kt-filemanager-table="rename"]')[0];
                                            if (renameBtn) {
                                                renameBtn.dataset.folderId = (response.id || '');
                                                renameBtn.dataset.nameEn = (response.name_en || nameEn.value.trim());
                                                renameBtn.dataset.nameAr = (response.name_ar || nameAr.value.trim());
                                            }
                                        }

                                        var rowCount = datatable.rows().count();
                                        var insertedData = datatable.row(rowCount - 1).data();
                                        for (var i = rowCount - 1; i > 0; i--) {
                                            var prevData = datatable.row(i - 1).data();
                                            datatable.row(i).data(prevData);
                                        }
                                        datatable.row(0).data(insertedData).draw(false);

                                        nameAr.value = '';
                                        nameEn.value = '';
                                        resetNewFolder();
                                    },
                                    error: function (xhr) {
                                        toastr.options = {
                                            "closeButton": true,
                                            "debug": false,
                                            "newestOnTop": false,
                                            "progressBar": false,
                                            "positionClass": "toastr-top-right",
                                            "preventDuplicates": false,
                                            "showDuration": "300",
                                            "hideDuration": "1000",
                                            "timeOut": "5000",
                                            "extendedTimeOut": "1000",
                                            "showEasing": "swing",
                                            "hideEasing": "linear",
                                            "showMethod": "fadeIn",
                                            "hideMethod": "fadeOut"
                                        };
                                        var errorMsg = 'Error creating folder.';
                                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                                            var errs = xhr.responseJSON.errors;
                                            if (errs.name_en && errs.name_en.length) {
                                                validator.updateMessage('name_en', 'notEmpty', errs.name_en[0]);
                                                validator.updateFieldStatus('name_en', 'Invalid');
                                            }
                                            if (errs.name_ar && errs.name_ar.length) {
                                                validator.updateMessage('name_ar', 'notEmpty', errs.name_ar[0]);
                                                validator.updateFieldStatus('name_ar', 'Invalid');
                                            }
                                            errorMsg = (errs.name_en && errs.name_en[0]) || (errs.name_ar && errs.name_ar[0]) || errorMsg;
                                            rowButton.removeAttribute("data-kt-indicator");
                                            return;
                                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMsg = xhr.responseJSON.message;
                                        }
                                        toastr.error(errorMsg);
                                        rowButton.removeAttribute("data-kt-indicator");
                                    }
                                });
                            } else {
                                // Disable indicator
                                rowButton.removeAttribute("data-kt-indicator");
                            }
                        });
                    }
                });

                // Handle cancel new folder button
                cancelButton.addEventListener('click', e => {
                    e.preventDefault();

                    // Activate indicator
                    cancelButton.setAttribute("data-kt-indicator", "on");

                    setTimeout(function () {
                        // Disable indicator
                        cancelButton.removeAttribute("data-kt-indicator");

                        // Toggle toastr
                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": false,
                            "progressBar": false,
                            "positionClass": "toastr-top-right",
                            "preventDuplicates": false,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };

                        toastr.error(__('Cancelled new folder creation'));
                        resetNewFolder();
                    }, 1000);
                });
            });
        }
    }

    // Reset add new folder input
    const resetNewFolder = () => {
        const newFolderRow = table.querySelector('#kt_file_manager_new_folder_row');

        if (newFolderRow) {
            newFolderRow.parentNode.removeChild(newFolderRow);
        }
    }

    // Handle rename file or folder
    const handleRename = () => {
        const renameButton = table.querySelectorAll('[data-kt-filemanager-table="rename"]');

        renameButton.forEach(button => {
            button.addEventListener('click', renameCallback);
        });
    }

    // Rename callback
    const renameCallback = (e) => {
        e.preventDefault();

        // Define shared value
        let nameValue;
        let nameArValue;
        let nameEnValue;

        // Stop renaming if there's an input existing
        if (table.querySelectorAll('#kt_file_manager_rename_input').length > 0) {
            Swal.fire({
                text: __("Unsaved input detected. Please save or cancel the current item"),
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: __("Ok, got it!"),
                customClass: {
                    confirmButton: "btn fw-bold btn-danger"
                }
            });

            return;
        }

        // Select parent row
        const parent = e.target.closest('tr');

        // Get name column
        const nameCol = parent.querySelectorAll('td')[1];
        const colIcon = nameCol.querySelector('.icon-wrapper');
        nameValue = nameCol.innerText.trim();
        const currentLink = nameCol.querySelector('a[href*="/dashboard/admin/reports/"]');
        const renameTrigger = e.target.closest('[data-kt-filemanager-table="rename"]');
        let folderId = renameTrigger && renameTrigger.dataset.folderId ? renameTrigger.dataset.folderId : null;
        let currentNameEn = renameTrigger && renameTrigger.dataset.nameEn ? renameTrigger.dataset.nameEn : null;
        let currentNameAr = renameTrigger && renameTrigger.dataset.nameAr ? renameTrigger.dataset.nameAr : null;
        if (!folderId && currentLink && currentLink.href) {
            const match = currentLink.href.match(/reports\/(\d+)/);
            folderId = match ? match[1] : null;
        }
        // Set rename input template
        const renameInput = renameTemplate.cloneNode(true);
        renameInput.querySelector('#kt_file_manager_rename_folder_icon').innerHTML = colIcon.outerHTML;

        // Swap current column content with input template
        nameCol.innerHTML = renameInput.innerHTML;

        // Set input value with current file/folder name
        const enInput = parent.querySelector('#name_en_input');
        const arInput = parent.querySelector('#name_ar_input');
        if (enInput) enInput.value = currentNameEn || nameValue;
        if (arInput) arInput.value = currentNameAr || nameValue;

        // Rename file / folder validator
        // var renameValidator = FormValidation.formValidation(
        //     nameCol,
        //     {
        //         fields: {
        //             'rename_folder_name': {
        //                 validators: {
        //                     notEmpty: {
        //                         message: 'Name is required'
        //                     }
        //                 }
        //             },
        //         },
        //         plugins: {
        //             trigger: new FormValidation.plugins.Trigger(),
        //             bootstrap: new FormValidation.plugins.Bootstrap5({
        //                 rowSelector: '.fv-row',
        //                 eleInvalidClass: '',
        //                 eleValidClass: ''
        //             })
        //         }
        //     }
        // );
        const renameValidator = FormValidation.formValidation(nameCol, {
            fields: {
                name_en: {
                    validators: {
                        notEmpty: { message: __('Name (English) is required') },
                        stringLength: { max: 190, message: __('Max 190 characters') }
                    }
                },
                name_ar: {
                    validators: {
                        notEmpty: { message: __('Name (Arabic) is required') },
                        stringLength: { max: 190, message: __('Max 190 characters') }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                }),
                message: new FormValidation.plugins.Message({
                    container: function (field) {
                        if (field === 'name_en') return parent.querySelector('#rename_name_en_validation');
                        if (field === 'name_ar') return parent.querySelector('#rename_name_ar_validation');
                        return nameCol;
                    },
                    class: 'invalid-feedback d-block mt-2'
                })
            }
        });



        // Rename input button action
        const renameInputButton = parent.querySelector('#kt_file_manager_rename_folder');
        if (renameInputButton) {
            renameInputButton.addEventListener('click', function (ev) {
                ev.preventDefault();
                if (!folderId) {
                    toastr.error(__('Unable to detect folder ID'));
                    return;
                }
                renameInputButton.setAttribute('data-kt-indicator', 'on');
                renameValidator.validate().then(function (status) {
                    if (status === 'Valid') {
                        const name_en = enInput ? enInput.value.trim() : '';
                        const name_ar = arInput ? arInput.value.trim() : '';
                        $.ajax({
                            url: '/dashboard/admin/reports/folder/' + folderId,
                            type: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            data: { name_en, name_ar },
                            success: function (response) {
                                const isAr = (window.isArabic === '1' || window.isArabic === true || window.isArabic === 'true');
                                const displayName = isAr ? (response.name_ar || name_ar) : (response.name_en || name_en);
                                const linkHref = '/dashboard/admin/reports/' + (response.id || folderId);
                                const newData = `<div class="d-flex align-items-center">${colIcon.outerHTML}<a href="${linkHref}" class="text-gray-800 text-hover-primary">${displayName}</a></div>`;
                                datatable.cell($(nameCol)).data(newData).draw();
                                const updatedRename = parent.querySelector('[data-kt-filemanager-table="rename"]');
                                if (updatedRename) {
                                    updatedRename.dataset.nameEn = response.name_en || name_en;
                                    updatedRename.dataset.nameAr = response.name_ar || name_ar;
                                    updatedRename.dataset.folderId = response.id || folderId;
                                }
                                toastr.success(__('Folder renamed successfully'));
                                renameInputButton.removeAttribute('data-kt-indicator');
                            },
                            error: function (xhr) {
                                var errorMsg = __('Error renaming folder');
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    var errs = xhr.responseJSON.errors;
                                    if (errs.name_en && errs.name_en.length) {
                                        renameValidator.updateMessage('name_en', 'notEmpty', errs.name_en[0]);
                                        renameValidator.updateFieldStatus('name_en', 'Invalid');
                                    }
                                    if (errs.name_ar && errs.name_ar.length) {
                                        renameValidator.updateMessage('name_ar', 'notEmpty', errs.name_ar[0]);
                                        renameValidator.updateFieldStatus('name_ar', 'Invalid');
                                    }
                                    renameInputButton.removeAttribute('data-kt-indicator');
                                    return;
                                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                toastr.error(errorMsg);
                                renameInputButton.removeAttribute('data-kt-indicator');
                            }
                        });
                    }
                    else {
                        renameInputButton.removeAttribute('data-kt-indicator');
                    }
                });
            });
        }


        // renameInputButton.addEventListener('click', e =>
        //      {
        //     console.log('validated!');
        //     e.preventDefault();
        //     // console.log('validated!');
        //     // Detect if validator is enabled
        //     // if (renameValidator) {
        //     //     renameValidator.validate().then(function (status) {
        //     //         console.log('validated!');

        //     //         if (status == 'Valid') {
        //     //             // Pop up confirmation
        //     //             Swal.fire({
        //     //                 text: __("Are you sure you want to rename ") + nameValue + "?",
        //     //                 icon: "warning",
        //     //                 showCancelButton: true,
        //     //                 buttonsStyling: false,
        //     //                 confirmButtonText: __("Yes, rename it!"),
        //     //                 cancelButtonText: __("No, cancel"),
        //     //                 customClass: {
        //     //                     confirmButton: "btn fw-bold btn-danger",
        //     //                     cancelButton: "btn fw-bold btn-active-light-primary"
        //     //                 }
        //     //             }).then(function (result) {
        //     //                 if (result.value) {
        //     //                     Swal.fire({
        //     //                         text: "You have renamed " + nameValue + "!.",
        //     //                         icon: "success",
        //     //                         buttonsStyling: false,
        //     //                         confirmButtonText: "Ok, got it!",
        //     //                         customClass: {
        //     //                             confirmButton: "btn fw-bold btn-primary",
        //     //                         }
        //     //                     }).then(function () {
        //     //                         // Get new file / folder name value
        //     //                         const newValue = document.querySelector('#kt_file_manager_rename_input').value;

        //     //                         // New column data template
        //     //                         const newData = `<div class="d-flex align-items-center">
        //     //                             ${colIcon.outerHTML}
        //     //                             <a href="?page=apps/file-manager/files/" class="text-gray-800 text-hover-primary">${newValue}</a>
        //     //                         </div>`;

        //     //                         // Draw datatable with new content -- Add more events here for any server-side events
        //     //                         datatable.cell($(nameCol)).data(newData).draw();
        //     //                     });
        //     //                 } else if (result.dismiss === 'cancel') {
        //     //                     Swal.fire({
        //     //                         text: nameValue + " was not renamed.",
        //     //                         icon: "error",
        //     //                         buttonsStyling: false,
        //     //                         confirmButtonText: "Ok, got it!",
        //     //                         customClass: {
        //     //                             confirmButton: "btn fw-bold btn-primary",
        //     //                         }
        //     //                     });
        //     //                 }
        //     //             });
        //     //         }
        //     //     });
        //     // }
        // });









        // Cancel rename input
        const cancelInputButton = parent.querySelector('#kt_file_manager_rename_folder_cancel');
        cancelInputButton.addEventListener('click', e => {
            e.preventDefault();

            // Simulate process for demo only
            cancelInputButton.setAttribute("data-kt-indicator", "on");

            setTimeout(function () {
                const revertHref = currentLink && currentLink.href ? currentLink.href : '#';
                const revertTemplate = `<div class="d-flex align-items-center">${colIcon.outerHTML}<a href="${revertHref}" class="text-gray-800 text-hover-primary">${nameValue}</a></div>`;

                // Remove spinner
                cancelInputButton.removeAttribute("data-kt-indicator");

                // Draw datatable with new content -- Add more events here for any server-side events
                datatable.cell($(nameCol)).data(revertTemplate).draw();

                // Toggle toastr
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toastr-top-right",
                    "preventDuplicates": false,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                toastr.error(__('Cancelled rename function'));
            }, 1000);
        });
    }

    // Init dropzone
    // const initDropzone = () => {
    //     // set the dropzone container id
    //     const id = "#kt_modal_upload_dropzone";
    //     const dropzone = document.querySelector(id);

    //     // set the preview element template
    //     var previewNode = dropzone.querySelector(".dropzone-item");
    //     previewNode.id = "";
    //     var previewTemplate = previewNode.parentNode.innerHTML;
    //     previewNode.parentNode.removeChild(previewNode);

    //     var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
    //         url: "path/to/your/server", // Set the url for your upload script location
    //         parallelUploads: 10,
    //         previewTemplate: previewTemplate,
    //         maxFilesize: 1, // Max filesize in MB
    //         autoProcessQueue: false, // Stop auto upload
    //         autoQueue: false, // Make sure the files aren't queued until manually added
    //         previewsContainer: id + " .dropzone-items", // Define the container to display the previews
    //         clickable: id + " .dropzone-select" // Define the element that should be used as click trigger to select files.
    //     });

    //     myDropzone.on("addedfile", function (file) {
    //         // Hook each start button
    //         file.previewElement.querySelector(id + " .dropzone-start").onclick = function () {
    //             // myDropzone.enqueueFile(file); -- default dropzone function

    //             // Process simulation for demo only
    //             const progressBar = file.previewElement.querySelector('.progress-bar');
    //             progressBar.style.opacity = "1";
    //             var width = 1;
    //             var timer = setInterval(function () {
    //                 if (width >= 100) {
    //                     myDropzone.emit("success", file);
    //                     myDropzone.emit("complete", file);
    //                     clearInterval(timer);
    //                 } else {
    //                     width++;
    //                     progressBar.style.width = width + '%';
    //                 }
    //             }, 20);
    //         };

    //         const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
    //         dropzoneItems.forEach(dropzoneItem => {
    //             dropzoneItem.style.display = '';
    //         });
    //         dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
    //         dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
    //     });

    //     // Hide the total progress bar when nothing's uploading anymore
    //     myDropzone.on("complete", function (file) {
    //         const progressBars = dropzone.querySelectorAll('.dz-complete');
    //         setTimeout(function () {
    //             progressBars.forEach(progressBar => {
    //                 progressBar.querySelector('.progress-bar').style.opacity = "0";
    //                 progressBar.querySelector('.progress').style.opacity = "0";
    //                 progressBar.querySelector('.dropzone-start').style.opacity = "0";
    //             });
    //         }, 300);
    //     });

    //     // Setup the buttons for all transfers
    //     dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
    //         // myDropzone.processQueue(); --- default dropzone process

    //         // Process simulation for demo only
    //         myDropzone.files.forEach(file => {
    //             const progressBar = file.previewElement.querySelector('.progress-bar');
    //             progressBar.style.opacity = "1";
    //             var width = 1;
    //             var timer = setInterval(function () {
    //                 if (width >= 100) {
    //                     myDropzone.emit("success", file);
    //                     myDropzone.emit("complete", file);
    //                     clearInterval(timer);
    //                 } else {
    //                     width++;
    //                     progressBar.style.width = width + '%';
    //                 }
    //             }, 20);
    //         });
    //     });

    //     // Setup the button for remove all files
    //     dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
    //         Swal.fire({
    //             text: "Are you sure you would like to remove all files?",
    //             icon: "warning",
    //             showCancelButton: true,
    //             buttonsStyling: false,
    //             confirmButtonText: "Yes, remove it!",
    //             cancelButtonText: "No, return",
    //             customClass: {
    //                 confirmButton: "btn btn-primary",
    //                 cancelButton: "btn btn-active-light"
    //             }
    //         }).then(function (result) {
    //             if (result.value) {
    //                 dropzone.querySelector('.dropzone-upload').style.display = "none";
    //                 dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    //                 myDropzone.removeAllFiles(true);
    //             } else if (result.dismiss === 'cancel') {
    //                 Swal.fire({
    //                     text: "Your files was not removed!.",
    //                     icon: "error",
    //                     buttonsStyling: false,
    //                     confirmButtonText: "Ok, got it!",
    //                     customClass: {
    //                         confirmButton: "btn btn-primary",
    //                     }
    //                 });
    //             }
    //         });
    //     });

    //     // On all files completed upload
    //     myDropzone.on("queuecomplete", function (progress) {
    //         const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
    //         uploadIcons.forEach(uploadIcon => {
    //             uploadIcon.style.display = "none";
    //         });
    //     });

    //     // On all files removed
    //     myDropzone.on("removedfile", function (file) {
    //         if (myDropzone.files.length < 1) {
    //             dropzone.querySelector('.dropzone-upload').style.display = "none";
    //             dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    //         }
    //     });
    // }

    // Init copy link
    const initCopyLink = () => {
        // Select all copy link elements
        const elements = table.querySelectorAll('[data-kt-filemanger-table="copy_link"]');

        elements.forEach(el => {
            // Define elements
            const button = el.querySelector('button');
            const generator = el.querySelector('[data-kt-filemanger-table="copy_link_generator"]');
            const result = el.querySelector('[data-kt-filemanger-table="copy_link_result"]');
            const input = el.querySelector('input');

            // Click action
            button.addEventListener('click', e => {
                e.preventDefault();

                // Reset toggle
                generator.classList.remove('d-none');
                result.classList.add('d-none');

                var linkTimeout;
                clearTimeout(linkTimeout);
                linkTimeout = setTimeout(() => {
                    generator.classList.add('d-none');
                    result.classList.remove('d-none');
                    input.select();
                }, 2000);
            });
        });
    }

    // Handle move to folder
    // const handleMoveToFolder = () => {
    //     const element = document.querySelector('#kt_modal_move_to_folder');
    //     // const form = element.querySelector('#kt_modal_move_to_folder_form');
    //     // const saveButton = form.querySelector('#kt_modal_move_to_folder_submit');
    //     const moveModal = new bootstrap.Modal(element);

    //     // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    //     var validator = FormValidation.formValidation(
    //         form,
    //         {
    //             fields: {
    //                 'move_to_folder': {
    //                     validators: {
    //                         notEmpty: {
    //                             message: 'Please select a folder.'
    //                         }
    //                     }
    //                 },
    //             },

    //             plugins: {
    //                 trigger: new FormValidation.plugins.Trigger(),
    //                 bootstrap: new FormValidation.plugins.Bootstrap5({
    //                     rowSelector: '.fv-row',
    //                     eleInvalidClass: '',
    //                     eleValidClass: ''
    //                 })
    //             }
    //         }
    //     );

    //     saveButton.addEventListener('click', e => {
    //         e.preventDefault();

    //         saveButton.setAttribute("data-kt-indicator", "on");

    //         if (validator) {
    //             validator.validate().then(function (status) {
    //                 console.log('validated!');

    //                 if (status == 'Valid') {
    //                     // Simulate process for demo only
    //                     setTimeout(function () {

    //                         Swal.fire({
    //                             text: "Are you sure you would like to move to this folder",
    //                             icon: "warning",
    //                             showCancelButton: true,
    //                             buttonsStyling: false,
    //                             confirmButtonText: "Yes, move it!",
    //                             cancelButtonText: "No, return",
    //                             customClass: {
    //                                 confirmButton: "btn btn-primary",
    //                                 cancelButton: "btn btn-active-light"
    //                             }
    //                         }).then(function (result) {
    //                             if (result.isConfirmed) {
    //                                 form.reset(); // Reset form
    //                                 moveModal.hide(); // Hide modal

    //                                 toastr.options = {
    //                                     "closeButton": true,
    //                                     "debug": false,
    //                                     "newestOnTop": false,
    //                                     "progressBar": false,
    //                                     "positionClass": "toastr-top-right",
    //                                     "preventDuplicates": false,
    //                                     "showDuration": "300",
    //                                     "hideDuration": "1000",
    //                                     "timeOut": "5000",
    //                                     "extendedTimeOut": "1000",
    //                                     "showEasing": "swing",
    //                                     "hideEasing": "linear",
    //                                     "showMethod": "fadeIn",
    //                                     "hideMethod": "fadeOut"
    //                                 };

    //                                 toastr.success('1 item has been moved.');

    //                                 saveButton.removeAttribute("data-kt-indicator");
    //                             } else {
    //                                 Swal.fire({
    //                                     text: "Your action has been cancelled!.",
    //                                     icon: "error",
    //                                     buttonsStyling: false,
    //                                     confirmButtonText: "Ok, got it!",
    //                                     customClass: {
    //                                         confirmButton: "btn btn-primary",
    //                                     }
    //                                 });

    //                                 saveButton.removeAttribute("data-kt-indicator");
    //                             }
    //                         });
    //                     }, 500);
    //                 } else {
    //                     saveButton.removeAttribute("data-kt-indicator");
    //                 }
    //             });
    //         }
    //     });
    // }

    // Count total number of items
    // const countTotalItems = () => {
    //     const counter = document.getElementById('kt_file_manager_items_counter');

    //     // Count total number of elements in datatable --- more info: https://datatables.net/reference/api/count()
    //     counter.innerText = datatable.rows().count() + ' items';
    // }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_file_manager_list');

            if (!table) {
                return;
            }

            initTemplates();
            initDatatable();
            initToggleToolbar();
            handleSearchDatatable();
            handleDeleteRows();
            handleNewFolder();
            // initDropzone();
            initCopyLink();
            handleRename();
            // handleMoveToFolder();
            // countTotalItems();
            KTMenu.createInstances();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTFileManagerList.init();
});
