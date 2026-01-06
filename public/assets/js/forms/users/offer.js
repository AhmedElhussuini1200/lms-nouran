function waitForElement(selector, callback) {
    const el = document.querySelector(selector);
    if (el) {
        document
            .getElementById("pagination-container")
            .style.setProperty("display", "none", "important");
        document
            .getElementById("offer-container")
            .style.setProperty("display", "block", "important");
        $("#no-results-alert").hide();

        callback(el);
    } else {
        // جرب كل 100ms لغاية ما يلاقي العنصر
        setTimeout(() => waitForElement(selector, callback), 100);
    }
}

waitForElement("#offer-container", function (container) {
    console.log("✅ offer-container found:", container);

    container.innerHTML = `
        <div class="d-flex align-items-center gap-2 mb-4 mt-5">
            <select id="perPageSelect" class="form-select form-select-sm w-auto">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
            </select>
            <span id="paginationInfo" class="text-muted small"></span>
        </div>
        <div id="offers"></div>
        <div id="paginationLinks" class="mt-3 d-flex justify-content-end"></div>
    `;

    // ✅ بعد ما نضبط الـ DOM، نكمل الكود عادي

    const perPageSelect = document.getElementById("perPageSelect");
    const offerDiv = document.getElementById("offers");
    const paginationLinks = document.getElementById("paginationLinks");
    const paginationInfo = document.getElementById("paginationInfo");

    let userId = user;
    let currentPage = 1;
    let perPage = parseInt(perPageSelect.value);

    function loadOffers(page = 1, perPage = 10) {
        fetch(
            `/dashboard/admin/offer/${userId.id}?page=${page}&per_page=${perPage}`
        )
            .then((response) => response.json())
            .then((data) => {
                renderOffers(data);
                renderPagination(data);
                currentPage = data.current_page;
            })
            .catch((err) => {
                offerDiv.innerHTML = `<p class="text-danger">Error loading offers</p>`;
            });
    }

    function renderOffers(data) {
        if (!data.data || data.data.length === 0) {
            $("#no-results-alert").fadeIn();
            // offerDiv.innerHTML = '<p>No offers available.</p>';
            return;
        }

        let table = `
        <div class="card-body pt-0">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr>
                        <th>${__("ID")}</th>
                        <th>${__("Mission")}</th>
                        <th>${__("User")}</th>
                        <th>${__("Status")}</th>
                        <th>${__("Budget")}</th>
                        <th>${__("Created at")}</th>
                    </tr>
                </thead>
                <tbody>`;

        data.data.forEach((offer) => {
            table += `
                <tr>
                    <td>${offer.id}</td>
                    <td>${offer.mission?.description ?? ""}</td>
                    <td>${user.full_name}</td>
                    <td>${offer.status?.name ?? ""}</td>
                    <td>${offer.available_budget ?? "N/A"}</td>
                    <td>${new Date(offer.created_at).toLocaleDateString()}</td>
                </tr>`;
        });

        table += `</tbody></table></div>`;
        offerDiv.innerHTML = table;

        paginationInfo.textContent = `Showing ${data.from} to ${data.to} of ${data.total} records`;
    }

    function renderPagination(data) {
        let html = "";

        const prevDisabled = data.current_page === 1 ? "disabled" : "";
        const nextDisabled =
            data.current_page === data.last_page ? "disabled" : "";

        html += `<button class="btn btn-sm btn-light mx-1 paginate-btn" data-page="${
            data.current_page - 1
        }" ${prevDisabled}>&lt;</button>`;

        for (let i = 1; i <= data.last_page; i++) {
            const active =
                i === data.current_page ? "btn-primary" : "btn-light";
            html += `<button class="btn btn-sm ${active} mx-1 paginate-btn" data-page="${i}">${i}</button>`;
        }

        html += `<button class="btn btn-sm btn-light mx-1 paginate-btn" data-page="${
            data.current_page + 1
        }" ${nextDisabled}>&gt;</button>`;

        paginationLinks.innerHTML = html;

        document.querySelectorAll(".paginate-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const page = parseInt(this.dataset.page);
                if (!isNaN(page)) loadOffers(page, perPage);
            });
        });
    }

    perPageSelect.addEventListener("change", function () {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadOffers(currentPage, perPage);
    });

    if (userId) {
        loadOffers(currentPage, perPage);
    }
});
