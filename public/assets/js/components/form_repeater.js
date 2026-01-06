$(document).ready(function () {
    var repeater = $("#form_repeater").repeater({
        initEmpty: false,
        isFirstItemUndeletable: true,
        show: function () {
            $(this).slideDown();
            $(this).find("input").prop("readonly", false);

            // أخفي زرار الحذف لأول عنصر بعد الإضافة
            $("#form_repeater [data-repeater-item]")
                .first()
                .find("[data-repeater-delete]")
                .hide();
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        },
    });
 
});
