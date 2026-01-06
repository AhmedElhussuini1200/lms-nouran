window['onAjaxSuccess'] = () => {
    $("#crud_modal").modal('hide')
    // datatable.draw();
    if (typeof datatable !== 'undefined') {
        datatable.draw(); // only runs if datatable is defined
    }
}

window['onAjaxError'] = (status, response) => {
    $(".restore-item").on('click', function (e) {
        e.preventDefault();
        UIBlocker.block();

        $.ajax({
            type: "get",
            url: $(this).attr('href'),
            success: function (data) {
                datatable.draw();

                $("#crud_modal").modal('hide')
                showToast(__("Item has been restored successfully"));
                removeValidationMessages();

                UIBlocker.release();
            }
        });
    });
}

window['onAjaxSuccessDragAndDrop'] = () => {
    $("#createBannerModal").modal('hide');
    retrieveBannersFormBackend();


}



$(document).ready(function () {

    // تهيئة كل select2
    $('[data-control="select2"]').each(function () {
        let parentModal = $(this).closest(".modal");
        $(this).select2({
            dropdownParent: parentModal.length ? parentModal : $(document.body),
            dir: $(this).data("dir") || "ltr",
            placeholder: $(this).data("placeholder") || "",
            width: "100%",
        });
    });

    // التعامل مع أي فورم AJAX
    $(".ajax-form").submit(function (event) {
        event.preventDefault();
        submitForm(this); // دالة AJAX الخاصة بك
    });

    // دالة تفريغ أي فورم بالكامل
    function resetFormCompletely(form) {
        form[0].reset(); // input, textarea, select عادي
        form.find('select[data-control="select2"]').val(null).trigger('change'); // select2
        form.find(".invalid-feedback").text(''); // رسائل خطأ
    }

    // عند فتح أي مودال، يتم تفريغ الفورم تلقائيًا
    $(".modal").on("show.bs.modal", function () {
        const form = $(this).find("form.ajax-form");
        if (form.length) resetFormCompletely(form);
    });

    // دالة النجاح العامة بعد أي AJAX
    window['onAjaxSuccess'] = function(response) {
        // 1- تفريغ الفورمات
        $('.ajax-form').each(function () {
            resetFormCompletely($(this));
        });

        // 2- إغلاق أي مودال مفتوح
        $('.modal.show').each(function () {
            const modal = bootstrap.Modal.getInstance(this);
            if (modal) modal.hide();
        });

        // 3- تحديث أي Datatable موجود
        if (typeof datatable !== 'undefined') {
            datatable.draw();
        }

        // 4- تحديث البنرات لو فيه Drag & Drop
        if (typeof retrieveBannersFormBackend === 'function') {
            retrieveBannersFormBackend();
        }
    };

    // دالة الخطأ العامة
    window['onAjaxError'] = function(status, response) {
        $(".restore-item").on('click', function (e) {
            e.preventDefault();
            UIBlocker.block();

            $.ajax({
                type: "get",
                url: $(this).attr('href'),
                success: function (data) {
                    if (typeof datatable !== 'undefined') datatable.draw();
                    $("#crud_modal").modal('hide');
                    showToast(__("Item has been restored successfully"));
                    $('.invalid-feedback').text('');
                    UIBlocker.release();
                }
            });
        });
    };
});

