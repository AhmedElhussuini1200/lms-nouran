let parentCategoriesSp = $("#parent-category-sp");

$(document).ready(() => {
    $.ajax({
        url: `/dashboard/admin/parent-categories`,
        method: "GET",
        success: (response) => {
            parentCategoriesSp.empty();

            parentCategoriesSp.append(`<option></option>`);
            console.log(response);

            response["parentCategories"].forEach((category) => {
                parentCategoriesSp.append(
                    `<option value="${category["id"]}" > ${category["name"]} </option>`
                );
            });
        },
    });
});
