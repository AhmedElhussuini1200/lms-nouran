"use strict";

$(document).ready(function () {
    var compaingData = function (start, end) {
        document.getElementById("count-user").textContent = 0;

        // Fetch data from the server using AJAX
        $.ajax({
            url: "/dashboard/admin/chart",
            type: "GET",
            data: {
                start: start.format("YYYY-MM-DD"),
                end: end.format("YYYY-MM-DD"),
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                document.getElementById("count-user").textContent =
                    data.totalUsers ?? 0;
                // Update the chart with the new data
                var KTChartsWidget26 = (function () {
                    var chart = {
                        self: null,
                        rendered: false,
                    };

                    var values = Object.values(data.chartData);
                    var userCount = values.map((item) => item.user_count); // Extract user_count
                    var date = values.map((item) => item.date); // Extract date
                    // Private methods
                    var initChart = function () {
                        var element = document.getElementById(
                            "kt_charts_count_user"
                        );

                        if (!element) {
                            return;
                        }

                        var height = parseInt(KTUtil.css(element, "height"));
                        var labelColor =
                            KTUtil.getCssVariableValue("--bs-gray-500");
                        var borderColor = KTUtil.getCssVariableValue(
                            "--bs-border-dashed-color"
                        );
                        var baseColor =
                            KTUtil.getCssVariableValue("--bs-primary");
                        var lightColor =
                            KTUtil.getCssVariableValue("--bs-primary");
                        var chartInfo =
                            element.getAttribute("data-kt-chart-info");

                        var options = {
                            series: [
                                {
                                    name: __("Users"),
                                    data:
                                        locale == "ar"
                                            ? [...userCount].reverse()
                                            : userCount,
                                },
                            ],
                            chart: {
                                // rtl: true,
                                fontFamily: "inherit",
                                type: "area",
                                height: height,
                                toolbar: {
                                    show: false,
                                },
                            },
                            plotOptions: {},
                            legend: {
                                show: false,
                            },
                            dataLabels: {
                                enabled: false,
                            },
                            fill: {
                                type: "gradient",
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.4,
                                    opacityTo: 0,
                                    stops: [0, 80, 100],
                                },
                            },
                            stroke: {
                                curve: "smooth",
                                show: true,
                                width: 3,
                                colors: [baseColor],
                            },
                            xaxis: {
                                categories:
                                    locale == "ar" ? [...date].reverse() : date,

                                axisBorder: {
                                    show: false,
                                },
                                axisTicks: {
                                    show: false,
                                },
                                tickAmount: 6,
                                labels: {
                                    rotate: 0,
                                    rotateAlways: true,
                                    style: {
                                        colors: labelColor,
                                        fontSize: "12px",
                                        align: "left",
                                    },
                                },
                                crosshairs: {
                                    position: "front",
                                    stroke: {
                                        color: baseColor,
                                        width: 1,
                                        dashArray: 3,
                                    },
                                },
                                tooltip: {
                                    enabled: true,
                                    formatter: undefined,
                                    offsetY: 0,
                                    style: {
                                        fontSize: "12px",
                                    },
                                },
                            },
                            yaxis: {
                                max: Math.max(...userCount),
                                min: Math.min(...userCount),
                                opposite: locale === "ar" ? true : false, // puts it on the right side in RTL
                                // tickAmount: 6,
                                labels: {
                                    style: {
                                        colors: labelColor,
                                        fontSize: "12px",
                                    },

                                    formatter: function (val) {
                                        return val;
                                    },
                                },
                            },
                            states: {
                                normal: {
                                    filter: {
                                        type: "none",
                                        value: 0,
                                    },
                                },
                                hover: {
                                    filter: {
                                        type: "none",
                                        value: 0,
                                    },
                                },
                                active: {
                                    allowMultipleDataPointsSelection: false,
                                    filter: {
                                        type: "none",
                                        value: 0,
                                    },
                                },
                            },
                            tooltip: {
                                style: {
                                    fontSize: "12px",
                                },
                                y: {
                                    formatter: function (val) {
                                        return val;
                                    },
                                },
                            },
                            colors: [lightColor],
                            grid: {
                                borderColor: borderColor,
                                strokeDashArray: 4,
                                yaxis: {
                                    lines: {
                                        show: true,
                                    },
                                },
                            },
                            markers: {
                                strokeColor: baseColor,
                                strokeWidth: 3,
                            },
                        };

                        chart.self = new ApexCharts(element, options);

                        // Set timeout to properly get the parent elements width
                        chart.self.render();
                        chart.rendered = true;
                    };

                    // Public methods
                    return {
                        init: function () {
                            initChart(chart);
                            if (chart.rendered) {
                                chart.self.destroy();
                            }
                            initChart(chart);
                        },
                    };
                })();
                KTChartsWidget26.init();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching campaign data:", error);
            },
        });
    };

    var createDateRangePickers = function () {
        // Check if jQuery is included
        if (typeof jQuery === "undefined") {
            return;
        }

        // Check if daterangepicker is included
        if (typeof $.fn.daterangepicker === "undefined") {
            return;
        }

        var elements = [].slice.call(
            document.querySelectorAll('[data-users-daterangepicker="true"]')
        );
        var start = moment().startOf("month");
        var end = moment().endOf("month");

        elements.forEach(function (element) {
            if (element.getAttribute("data-users-initialized") === "1") {
                return;
            }

            var display = element.querySelector("div");
            var attrOpens = element.hasAttribute(
                "data-users-daterangepicker-opens"
            )
                ? element.getAttribute("data-users-daterangepicker-opens")
                : "left";
            var range = element.getAttribute(
                "data-users-daterangepicker-range"
            );
            var cb = function (start, end) {
                var current = moment();
                compaingData(start, end);
                if (display) {
                    if (
                        current.isSame(start, "day") &&
                        current.isSame(end, "day")
                    ) {
                        display.innerHTML = start.format("D/M/YYYY");
                    } else {
                        display.innerHTML =
                            start.format("D/M/YYYY") +
                            " - " +
                            end.format("D/M/YYYY");
                    }
                }
            };
            // Handle special cases for date range selection
            if (range === "today") {
                start = moment();
                end = moment();
            }

            // Initialize daterangepicker
            $(element).daterangepicker(
                {
                    startDate: start,
                    endDate: end,
                    opens: attrOpens,
                    showCustomRangeLabel: false,
                    ranges: {
                        [__("Today")]: [moment(), moment()],
                        [__("Last 7 Days")]: [
                            moment().subtract(6, "days"),
                            moment(),
                        ],
                        [__("This Month")]: [
                            moment().startOf("month"),
                            moment().endOf("month"),
                        ],
                        [__("Last Month")]: [
                            moment().subtract(1, "month").startOf("month"),
                            moment().subtract(1, "month").endOf("month"),
                        ],
                        [__("This Year")]: [
                            moment().startOf("year"),
                            moment().endOf("year"),
                        ],
                        [__("Last Year")]: [
                            moment().subtract(1, "year").startOf("year"),
                            moment().subtract(1, "year").endOf("year"),
                        ],
                    },
                },
                cb
            );

            // Call the callback function once to display the initial range
            cb(start, end);

            // Mark element as initialized to prevent reinitialization
            element.setAttribute("data-users-initialized", "1");
        });
    };
    createDateRangePickers();

    // Initialize the date range picker for the first time Most Popular Providers
    var ProvidersDateRangePicker = function (start, end) {
        document.getElementById("count-mission-deliveries").textContent = `${
            __("Total") + " 0 " + __("deliveries")
        }`;

        // Fetch data from the server using AJAX
        $.ajax({
            url: "/dashboard/admin/most-popular-providers",
            type: "GET",
            data: {
                start: start.format("YYYY-MM-DD"),
                end: end.format("YYYY-MM-DD"),
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                document.getElementById(
                    "count-mission-deliveries"
                ).textContent = `${
                    __("Total") +
                    ` ${data.countMissionDeliveries} ` +
                    __("deliveries")
                }`;
                const tbody = document.getElementById("providers-table-body"); // or any container
                tbody.innerHTML = "";
                if (
                    !data.mostPopularProviders ||
                    data.mostPopularProviders.length === 0
                ) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">
                                ${__("No popular providers found")}
                            </td>
                        </tr>
                    `;
                } else {
                    data.mostPopularProviders.forEach(function (provider) {
                        const row = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <img src="${
                                            provider.full_image_path
                                        }" class="" alt="" />
                                    </div>
                                    <div class="d-flex justify-content-start flex-column">
                                        <a href="/dashboard/admin/users/${
                                            provider.id
                                        }" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">
                                            ${provider.full_name}
                                        </a>
                                        <span class="text-gray-500 fw-semibold d-block fs-7">
                                            ${provider.field?.name || ""}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="text-gray-800 fw-bold d-block mb-1 fs-6">
                                    ${Number(
                                        provider.total_deliveries
                                    ).toLocaleString()}
                                </span>
                                <span class="fw-semibold text-gray-500 d-block">${__(
                                    "Deliveries"
                                )}</span>
                            </td>
                            <td class="text-end">
                                <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">
                                    <img alt="riyal" src="/placeholder_images/riyal_logo.svg" class="w-15px h-15px ms-1 me-1" />
                                    ${Number(
                                        provider.total_earnings
                                    ).toLocaleString()}
                                </a>
                                <span class="text-gray-500 fw-semibold d-block fs-7">${__(
                                    "Earnings"
                                )}</span>
                            </td>
                            <td class="float-end text-end border-0">
                                <div class="rating">
                                    ${[1, 2, 3, 4, 5]
                                        .map(
                                            (i) => `
                                        <div class="rating-label ${
                                            i <=
                                            Math.floor(provider.average_rating)
                                                ? "checked"
                                                : ""
                                        }">
                                            <i class="ki-outline ki-star fs-6"></i>
                                        </div>
                                    `
                                        )
                                        .join("")}
                                </div>
                                <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">${__(
                                    "Rating"
                                )}</span>
                            </td>
                            <td class="text-end">
                                <a href="/dashboard/admin/users/${
                                    provider.id
                                }" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                    <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                </a>
                            </td>
                        </tr>
                        `;

                        tbody.insertAdjacentHTML("beforeend", row);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching campaign data:", error);
            },
        });
    };
    var createDateProviderRangePickers = function () {
        // Check if jQuery is included
        if (typeof jQuery === "undefined") {
            return;
        }

        // Check if daterangepicker is included
        if (typeof $.fn.daterangepicker === "undefined") {
            return;
        }

        var elements = [].slice.call(
            document.querySelectorAll('[data-providers-daterangepicker="true"]')
        );
        var start = moment().startOf("month");
        var end = moment().endOf("month");

        elements.forEach(function (element) {
            if (element.getAttribute("data-providers-initialized") === "1") {
                return;
            }

            var display = element.querySelector("div");
            var attrOpens = element.hasAttribute(
                "data-providers-daterangepicker-opens"
            )
                ? element.getAttribute("data-providers-daterangepicker-opens")
                : "left";
            var range = element.getAttribute(
                "data-providers-daterangepicker-range"
            );
            var cb = function (start, end) {
                var current = moment();
                ProvidersDateRangePicker(start, end);
                if (display) {
                    if (
                        current.isSame(start, "day") &&
                        current.isSame(end, "day")
                    ) {
                        display.innerHTML = start.format("D/M/YYYY");
                    } else {
                        display.innerHTML =
                            start.format("D/M/YYYY") +
                            " - " +
                            end.format("D/M/YYYY");
                    }
                }
            };
            // Handle special cases for date range selection
            if (range === "today") {
                start = moment();
                end = moment();
            }

            // Initialize daterangepicker
            $(element).daterangepicker(
                {
                    startDate: start,
                    endDate: end,
                    opens: attrOpens,
                    showCustomRangeLabel: false,
                    ranges: {
                        [__("Today")]: [moment(), moment()],
                        [__("Last 7 Days")]: [
                            moment().subtract(6, "days"),
                            moment(),
                        ],
                        [__("This Month")]: [
                            moment().startOf("month"),
                            moment().endOf("month"),
                        ],
                        [__("Last Month")]: [
                            moment().subtract(1, "month").startOf("month"),
                            moment().subtract(1, "month").endOf("month"),
                        ],
                        [__("This Year")]: [
                            moment().startOf("year"),
                            moment().endOf("year"),
                        ],
                        [__("Last Year")]: [
                            moment().subtract(1, "year").startOf("year"),
                            moment().subtract(1, "year").endOf("year"),
                        ],
                    },
                },
                cb
            );

            // Call the callback function once to display the initial range
            cb(start, end);

            // Mark element as initialized to prevent reinitialization
            element.setAttribute("data-providers-initialized", "1");
        });
    };
    createDateProviderRangePickers();
    // get top cities based user
    var KTChartsTopCities = (function () {
        var chart = {
            self: null,
            rendered: false,
        };
        // Private methods
        const cityNames = topCitiesBasedUser.map((item) => item.city);
        const userCount = topCitiesBasedUser.map((item) => item.user_count);
        const maxUserCount = Math.max(
            ...topCitiesBasedUser.map((item) => item.user_count)
        );
        var initChart = function (chart) {
            var element = document.getElementById("kt_charts_top_cities");

            if (!element) {
                return;
            }

            var labelColor = KTUtil.getCssVariableValue("--bs-gray-800");
            var borderColor = KTUtil.getCssVariableValue(
                "--bs-border-dashed-color"
            );
            var maxValue = maxUserCount;

            var options = {
                series: [
                    {
                        name: __("Users"),
                        data: userCount,
                    },
                ],
                chart: {
                    fontFamily: "inherit",
                    type: "bar",
                    height: 350,
                    rtl: true,
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        horizontal: true,
                        distributed: true,
                        barHeight: 50,
                        dataLabels: {
                            position: locale == "ar" ? "top" : "bottom", // use 'bottom' for left and 'top' for right align(textAnchor)
                        },
                    },
                },
                dataLabels: {
                    // Docs: https://apexcharts.com/docs/options/datalabels/
                    enabled: true,
                    textAnchor: "start",
                    offsetX: 0,
                    formatter: function (val, opts) {
                        return val;
                    },
                    style: {
                        fontSize: "14px",
                        fontWeight: "600",
                        // align: 'left',
                    },
                },
                legend: {
                    show: false,
                },
                colors: ["#3E97FF", "#F1416C", "#50CD89", "#FFC700", "#7239EA"],
                xaxis: {
                    categories: [...cityNames].reverse(), // Reverse the order of categories for RTL
                    reversed: true,

                    labels: {
                        formatter: function (val) {
                            return val;
                        },
                        style: {
                            colors: labelColor,
                            fontSize: "14px",
                            fontWeight: "600",
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    opposite: locale === "ar" ? true : false, // puts it on the right side in RTL
                    labels: {
                        formatter: function (val, opt) {
                            if (Number.isInteger(val)) {
                                var percentage = parseInt(
                                    (val * 100) / maxValue
                                ).toString();
                                return val + " - " + percentage + "%";
                            } else {
                                return val;
                            }
                        },
                        style: {
                            colors: labelColor,
                            fontSize: "14px",
                            fontWeight: "600",
                        },
                        offsetY: 2,
                    },
                },
                grid: {
                    borderColor: borderColor,
                    xaxis: {
                        lines: {
                            show: true,
                        },
                    },
                    yaxis: {
                        lines: {
                            show: false,
                        },
                    },
                    strokeDashArray: 4,
                },
                tooltip: {
                    style: {
                        fontSize: "12px",
                    },
                    y: {
                        formatter: function (val) {
                            return val;
                        },
                    },
                },
            };

            chart.self = new ApexCharts(element, options);

            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart.self.render();
                chart.rendered = true;
            }, 200);
        };

        // Public methods
        return {
            init: function () {
                initChart(chart);

                // Update chart on theme mode change
                KTThemeMode.on("kt.thememode.change", function () {
                    if (chart.rendered) {
                        chart.self.destroy();
                    }

                    initChart(chart);
                });
            },
        };
    })();
    KTChartsTopCities.init();

    // In-Progress Missions This Month
    var KTCardWidgetInProgress = (function () {
        var chart = {
            self: null,
            rendered: false,
        };

        var inProgress = Object.values(inProgressMissions);
        var date = inProgress.map((item) => item.date); // Extract date
        var missionCount = inProgress.map((item) => item.mission_count); // Extract mission_count
        var totalCommission = inProgress.map((item) => item.total_commission); // Extract total_commission
        var reversedDate = locale === "ar" ? [...date].reverse() : date; // Reverse the order for RTL
        // Private methods
        var initChart = function (chart) {
            var element = document.getElementById(
                "kt_card_widget_in_progress_chart"
            );

            if (!element) {
                return;
            }

            var height = parseInt(KTUtil.css(element, "height"));
            var borderColor = KTUtil.getCssVariableValue(
                "--bs-border-dashed-color"
            );
            var baseColor = KTUtil.getCssVariableValue("--bs-gray-800");
            var lightColor = KTUtil.getCssVariableValue("--bs-success");

            var options = {
                series: [
                    {
                        name: __("Missions"),
                        data:
                            locale === "ar"
                                ? [...missionCount].reverse()
                                : missionCount, // Reverse the order for RTL
                    },
                    {
                        name: __("Total"),
                        data:
                            locale === "ar"
                                ? [...totalCommission].reverse()
                                : totalCommission, // Reverse the order for RTL
                    },
                ],
                chart: {
                    fontFamily: "inherit",
                    type: "area",
                    height: height,
                    toolbar: {
                        show: false,
                    },
                },
                legend: {
                    show: false,
                },
                dataLabels: {
                    enabled: false,
                },
                fill: {
                    type: "solid",
                    opacity: 0,
                },
                stroke: {
                    curve: "smooth",
                    show: true,
                    width: 2,
                    colors: [baseColor],
                },
                xaxis: {
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                    // reversed: locale === 'ar',

                    tickPlacement: "between",
                    labels: {
                        show: false,
                        rotate: 0,
                        rotateAlways: true,
                        // align: locale == "ar" ? 'end' : 'left',
                    },
                    crosshairs: {
                        position: "front",
                        stroke: {
                            color: baseColor,
                            width: 1,
                            dashArray: 3,
                        },
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: "12px",
                            // align: locale == "ar" ? 'end' : 'left',
                        },
                    },
                },
                yaxis: {
                    opposite: locale === "ar", // puts it on the right side in RTL

                    labels: {
                        show: false,
                        align: locale == "ar" ? "end" : "left",
                    },
                },
                states: {
                    normal: {
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                    hover: {
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,

                    style: {
                        fontSize: "12px",
                        // align: locale == "ar" ? 'end' : 'left',
                    },
                    x: {
                        formatter: function (_, { dataPointIndex }) {
                            return reversedDate[dataPointIndex]; // Already formatted in PHP
                        },
                    },
                    y: {
                        formatter: function (val, { seriesIndex }) {
                            if (seriesIndex === 0) {
                                return Number(val).toLocaleString(); // or "مهمة" if localized
                            } else if (seriesIndex === 1) {
                                return `<div dir="ltr"><img src="/placeholder_images/riyal_logo.svg" style="width:13px;height:13px;" /> ${Number(
                                    val
                                ).toLocaleString()}</div>`; // or "ريال" if localized
                            }
                            return val;
                        },
                    },
                },
                colors: [lightColor, "#002F63"],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: -20,
                        bottom: -20,
                        left: -20,
                    },
                    yaxis: {
                        lines: {
                            show: true,
                        },
                    },
                },
                markers: {
                    strokeColor: baseColor,
                    strokeWidth: 2,
                },
            };

            chart.self = new ApexCharts(element, options);

            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart.self.render();
                chart.rendered = true;
            }, 200);
        };

        // Public methods
        return {
            init: function () {
                initChart(chart);

                // Update chart on theme mode change
                KTThemeMode.on("kt.thememode.change", function () {
                    if (chart.rendered) {
                        chart.self.destroy();
                    }

                    initChart(chart);
                });
            },
        };
    })();
    KTCardWidgetInProgress.init();

    // Delivered Missions This Month
    var KTCardWidgetDelivered = (function () {
        var chart = {
            self: null,
            rendered: false,
        };

        var delivered = Object.values(deliveredMissions);
        var date = delivered.map((item) => item.date); // Extract date
        var missionCount = delivered.map((item) => item.mission_count); // Extract mission_count
        var totalCommission = delivered.map((item) => item.total_commission); // Extract total_commissions
        var reverseDate = locale == "ar" ? [...date].reverse() : date;
        // Private methods
        var initChart = function (chart) {
            var element = document.getElementById(
                "kt_card_widget_deliveried_chart"
            );

            if (!element) {
                return;
            }

            var height = parseInt(KTUtil.css(element, "height"));
            var borderColor = KTUtil.getCssVariableValue(
                "--bs-border-dashed-color"
            );
            var baseColor = KTUtil.getCssVariableValue("--bs-gray-800");
            var lightColor = KTUtil.getCssVariableValue("--bs-success");

            var options = {
                series: [
                    {
                        name: __("Missions"),
                        data:
                            locale == "ar"
                                ? [...missionCount].reverse()
                                : missionCount,
                    },
                    {
                        name: __("Total"),
                        data:
                            locale == "ar"
                                ? [...totalCommission].reverse()
                                : totalCommission,
                    },
                ],
                chart: {
                    fontFamily: "inherit",
                    type: "area",
                    height: height,
                    toolbar: {
                        show: false,
                    },
                },
                legend: {
                    show: false,
                },
                dataLabels: {
                    enabled: false,
                },
                fill: {
                    type: "solid",
                    opacity: 0,
                },
                stroke: {
                    curve: "smooth",
                    show: true,
                    width: 2,
                    colors: [baseColor],
                },
                xaxis: {
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: false,
                        rotate: 0,
                        rotateAlways: true,
                    },
                    crosshairs: {
                        position: "front",
                        stroke: {
                            color: baseColor,
                            width: 1,
                            dashArray: 3,
                        },
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: "12px",
                        },
                    },
                },
                yaxis: {
                    opposite: locale === "ar", // puts it on the right side in RTL

                    labels: {
                        show: false,
                    },
                },
                states: {
                    normal: {
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                    hover: {
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: "none",
                            value: 0,
                        },
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,

                    style: {
                        fontSize: "12px",
                    },
                    x: {
                        formatter: function (_, { dataPointIndex }) {
                            return reverseDate[dataPointIndex]; // Already formatted in PHP
                        },
                    },
                    y: {
                        formatter: function (val, { seriesIndex }) {
                            if (seriesIndex === 0) {
                                return Number(val).toLocaleString(); // or "مهمة" if localized
                            } else if (seriesIndex === 1) {
                                return `<div dir="ltr"><img src="/placeholder_images/riyal_logo.svg" style="width:13px;height:13px;" /> ${Number(
                                    val
                                ).toLocaleString()}</div>`; // or "ريال" if localized
                            }
                            console.log(seriesIndex, val);
                            return val;
                        },
                    },
                },
                colors: [lightColor, "#002F63"],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: -20,
                        bottom: -20,
                        left: -20,
                    },
                    yaxis: {
                        lines: {
                            show: true,
                        },
                    },
                },
                markers: {
                    strokeColor: baseColor,
                    strokeWidth: 2,
                },
            };

            chart.self = new ApexCharts(element, options);

            // Set timeout to properly get the parent elements width
            setTimeout(function () {
                chart.self.render();
                chart.rendered = true;
            }, 200);
        };

        // Public methods
        return {
            init: function () {
                initChart(chart);

                // Update chart on theme mode change
                KTThemeMode.on("kt.thememode.change", function () {
                    if (chart.rendered) {
                        chart.self.destroy();
                    }

                    initChart(chart);
                });
            },
        };
    })();
    KTCardWidgetDelivered.init();

    // User approved , not valid
    var KTCardsWidgetUsersApproved = (function () {
        // Private methods
        var initChart = function () {
            var el = document.getElementById(
                "kt_card_widget_users_approved_chart"
            );

            if (!el) {
                return;
            }

            var options = {
                size: el.getAttribute("data-kt-size")
                    ? parseInt(el.getAttribute("data-kt-size"))
                    : 70,
                lineWidth: el.getAttribute("data-kt-line")
                    ? parseInt(el.getAttribute("data-kt-line"))
                    : 11,
                rotate: el.getAttribute("data-kt-rotate")
                    ? parseInt(el.getAttribute("data-kt-rotate"))
                    : 145,
                approvedPercent: parseFloat(
                    el.getAttribute("data-approved") || 0
                ),
                notApprovedPercent: parseFloat(
                    el.getAttribute("data-not-approved") || 0
                ),
                //percent:  el.getAttribute('data-kt-percent') ,
            };

            var canvas = document.createElement("canvas");
            var span = document.createElement("span");

            if (typeof G_vmlCanvasManager !== "undefined") {
                G_vmlCanvasManager.initElement(canvas);
            }

            var ctx = canvas.getContext("2d");
            canvas.width = canvas.height = options.size;

            el.appendChild(span);
            el.appendChild(canvas);

            ctx.translate(options.size / 2, options.size / 2); // change center
            ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI); // rotate -90 deg

            //imd = ctx.getImageData(0, 0, 240, 240);
            var radius = (options.size - options.lineWidth) / 2;

            var drawCircle = function (color, lineWidth, percent, startAngle) {
                percent = Math.min(Math.max(0, percent || 1), 1);
                ctx.beginPath();
                ctx.arc(
                    0,
                    0,
                    radius,
                    startAngle,
                    startAngle + Math.PI * 2 * percent,
                    false
                );
                // ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
                ctx.strokeStyle = color;
                ctx.lineCap = "round"; // butt, round or square
                ctx.lineWidth = lineWidth;
                ctx.stroke();
            };
            // Init
            drawCircle("#E4E6EF", options.lineWidth, 1, 0);
            // Approved (green)
            if (options.approvedPercent > 0) {
                drawCircle(
                    KTUtil.getCssVariableValue("--bs-success"),
                    options.lineWidth,
                    options.approvedPercent / 100,
                    0
                );
            }
            // Not Approved (red)
            if (options.notApprovedPercent > 0) {
                drawCircle(
                    KTUtil.getCssVariableValue("--bs-danger"),
                    options.lineWidth,
                    options.notApprovedPercent / 100,
                    Math.PI * 2 * (options.approvedPercent / 100)
                );
            }
        };

        // Public methods
        return {
            init: function () {
                initChart();
            },
        };
    })();
    KTCardsWidgetUsersApproved.init();

    // All missions and status

    var allMissionsStatus = function (start, end) {
        document.getElementById("all").textContent = `(0)`;
        document.getElementById("pending").textContent = `(0)`;
        document.getElementById("completed").textContent = `(0)`;
        document.getElementById("arbitration").textContent = `(0)`;

        // Fetch data from the server using AJAX
        $.ajax({
            url: "/dashboard/admin/all-missions-status",
            type: "GET",
            data: {
                start: start.format("YYYY-MM-DD"),
                end: end.format("YYYY-MM-DD"),
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $("#missions-body").html(`
            <tr id="loading-row" class = 'd-flex justify-content-center align-items-center h-100 w-100'>
                <td colspan="5" class="text-center py-5 w-100">
                    <span class="spinner-border text-primary text-center" role="status"></span>
                </td>
            </tr>
        `);
            },
            success: function (data) {
                $("#loading-row").remove();
                document.getElementById("all").textContent = `(${data.all})`;
                document.getElementById(
                    "pending"
                ).textContent = `(${data.in_progress})`;
                document.getElementById(
                    "completed"
                ).textContent = `(${data.completed})`;
                document.getElementById(
                    "arbitration"
                ).textContent = `(${data.arbitration})`;

                const tabs = document.querySelectorAll(
                    '[data-kt-table-widget-3="tab"]'
                );
                const contents =
                    document.querySelectorAll("[data-tab-content]");

                tabs.forEach((tab) => {
                    tab.addEventListener("click", () => {
                        const value = tab.getAttribute(
                            "data-kt-table-widget-3-value"
                        );

                        // Handle tab styling
                        tabs.forEach((t) => {
                            t.classList.remove(
                                "border-bottom",
                                "border-3",
                                "border-primary",
                                "text-dark"
                            );
                            t.classList.add("text-muted");
                        });
                        tab.classList.add(
                            "border-bottom",
                            "border-3",
                            "border-primary",
                            "text-dark"
                        );
                        tab.classList.remove("text-muted");

                        // Handle content display
                        contents.forEach((content) => {
                            if (
                                content.getAttribute("data-tab-content") ===
                                value
                            ) {
                                content.classList.remove("d-none");
                            } else {
                                content.classList.add("d-none");
                            }
                        });
                        const statusKey = value === "Show All" ? "all" : value;
                        renderMissionsByStatus(statusKey, data);
                    });
                });
                renderMissionsByStatus("all", data);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            },
        });
    };
    function getRandomBgColor() {
        var bgColors = [
            "bg-primary", // بنفسجي
            "bg-warning", // أصفر
            "bg-success", // أخضر
            "bg-danger", // أحمر
            "bg-info", // أزرق
            "bg-dark", // رمادي غامق أو أي لون تحبه
        ];
        return bgColors[Math.floor(Math.random() * bgColors.length)];
    }
    function generateMissionRow(mission, row, tbody) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        var avatarUnPaid = "";
        var avatarDelivered = "";
        if (mission.user && mission.user?.image) {
            avatarUnPaid = `<img src="${mission.user.full_image_path}" alt="" />`;
        } else if (mission.user) {
            avatarUnPaid = `<span class="symbol-label ${getRandomBgColor()} text-inverse fw-bold" style="color: white">
                                                    ${mission.user.first_name
                                                        .trim()
                                                        .charAt(0)}
                                                </span>`;
        }
        if (
            mission.offer_accepted?.user &&
            mission.offer_accepted?.user.image
        ) {
            avatarDelivered = `<img src="${mission.offer_accepted?.user.full_image_path}" alt="" />`;
        } else if (mission.offer_accepted?.user) {
            avatarDelivered = `<span class="symbol-label ${getRandomBgColor()} text-inverse fw-bold" style="color: white">
                                                    ${mission.offer_accepted?.user.first_name
                                                        .trim()
                                                        .charAt(0)}
                                                </span>`;
        }
        row.innerHTML = `
                <td class="min-w-175px">
                    <div class="position-relative ps-6 pe-3 py-2">
                        <div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 ${getRandomBgColor()}"></div>
                        <a href="dashboard/missions/${
                            mission.id
                        }" class="mb-1 text-gray-900 text-hover-primary fw-bold">${
            mission.description
        }</a>
                        <div class="fs-7 text-muted fw-bold">${__(
                            "Created on"
                        )} ${new Intl.DateTimeFormat(
            locale == "ar" ? "ar-GB" : "en-GB",
            options
        ).format(new Date(mission.created_at))}</div>
                    </div>
                </td>

                <td>
                    <span class="badge badge-light-${
                        mission.last_statue.status.color
                    }">${mission.last_statue.status.name}</span>
                </td>
                <td class="min-w-125px">
                    <!--begin::Team members-->
                    <div class="symbol-group symbol-hover mb-1">
                        <!--begin::Member-->
                        <div class="symbol symbol-circle symbol-25px">
                            ${avatarUnPaid}
                        </div>
                        <!--end:: Member-- >
                        <!--begin:: Member-->
                        <div class="symbol symbol-circle symbol-25px">
                            ${avatarDelivered}
                        </div>
                        <!--end:: Member-->
                    </div>
                    <!--end::Team members-->
                <div class="fs-7 fw-bold text-muted">${__("Team Members")}</div>
                </td>
                <td class="min-w-150px">
                    <div class="mb-2 fw-bold">${new Intl.DateTimeFormat(
                        locale == "ar" ? "ar-GB" : "en-GB",
                        options
                    ).format(new Date(mission.delivery_time))}</div>
                    <div class="fs-7 fw-bold text-muted">${__(
                        "Delivery Time"
                    )}</div>
                </td>
                <td class="text-end">
                    <a href="dashboard/missions/${mission.id}"
                        class="btn btn-icon btn-sm btn-light btn-active-primary w-25px h-25px">
                        <i class="ki-outline ki-black-right fs-2 text-muted"></i>
                    </a>
                </td>
            `;

        tbody.appendChild(row);
    }
    function renderMissionsByStatus(status = "all", missions) {
        const tbody = document.querySelector(
            `[data-kt-table-widget-3="${status}"] tbody`
        );
        tbody.innerHTML = "";

        if (status === "all") {
            if (missions.all_data.length > 0) {
                missions.all_data.forEach((mission) => {
                    const row = document.createElement("tr");
                    generateMissionRow(mission, row, tbody);
                });
            } else {
                const row = document.createElement("tr");
                row.innerHTML = `
                        <td colspan="5" class="dt-empty text-center">${__(
                            "No data available in table"
                        )}</td>
                    `;
                tbody.appendChild(row);
            }
        } else if (status === "Pending") {
            if (missions.missions_in_progress.length > 0) {
                missions.missions_in_progress.forEach((mission) => {
                    const row = document.createElement("tr");
                    generateMissionRow(mission, row, tbody);
                });
            } else {
                const row = document.createElement("tr");
                row.innerHTML = `
                        <td colspan="5" class="dt-empty text-center">${__(
                            "No data available in table"
                        )}</td>
                    `;
                tbody.appendChild(row);
            }
        } else if (status === "Completed") {
            if (missions.missions_completed.length > 0) {
                missions.missions_completed.forEach((mission) => {
                    const row = document.createElement("tr");
                    generateMissionRow(mission, row, tbody);
                });
            } else {
                const row = document.createElement("tr");
                row.innerHTML = `
                        <td colspan="5" class="dt-empty text-center">${__(
                            "No data available in table"
                        )}</td>
                    `;
                tbody.appendChild(row);
            }
        } else if (status === "Arbitration") {
            if (missions.missions_under_arbitration.length > 0) {
                missions.missions_under_arbitration.forEach((mission) => {
                    const row = document.createElement("tr");
                    generateMissionRow(mission, row, tbody);
                });
            } else {
                const row = document.createElement("tr");
                row.innerHTML = `
                        <td colspan="5" class="dt-empty text-center">${__(
                            "No data available in table"
                        )}</td>
                    `;
                tbody.appendChild(row);
            }
        }
    }

    var createDateMissionStatusRangePickers = function () {
        // Check if jQuery is included
        if (typeof jQuery === "undefined") {
            return;
        }

        // Check if daterangepicker is included
        if (typeof $.fn.daterangepicker === "undefined") {
            return;
        }

        var elements = [].slice.call(
            document.querySelectorAll('[data-missions-daterangepicker="true"]')
        );
        var start = moment().startOf("month");
        var end = moment().endOf("month");

        elements.forEach(function (element) {
            if (element.getAttribute("data-missions-initialized") === "1") {
                return;
            }

            var display = element.querySelector("div");
            var attrOpens = element.hasAttribute(
                "data-missions-daterangepicker-opens"
            )
                ? element.getAttribute("data-missions-daterangepicker-opens")
                : "left";
            var range = element.getAttribute(
                "data-missions-daterangepicker-range"
            );
            var cb = function (start, end) {
                var current = moment();
                allMissionsStatus(start, end);
                if (display) {
                    if (
                        current.isSame(start, "day") &&
                        current.isSame(end, "day")
                    ) {
                        display.innerHTML = start.format("D/M/YYYY");
                    } else {
                        display.innerHTML =
                            start.format("D/M/YYYY") +
                            " - " +
                            end.format("D/M/YYYY");
                    }
                }
            };
            // Handle special cases for date range selection
            if (range === "today") {
                start = moment();
                end = moment();
            }

            // Initialize daterangepicker
            $(element).daterangepicker(
                {
                    startDate: start,
                    endDate: end,
                    opens: attrOpens,
                    showCustomRangeLabel: false,
                    ranges: {
                        [__("Today")]: [moment(), moment()],
                        [__("Last 7 Days")]: [
                            moment().subtract(6, "days"),
                            moment(),
                        ],
                        [__("This Month")]: [
                            moment().startOf("month"),
                            moment().endOf("month"),
                        ],
                        [__("Last Month")]: [
                            moment().subtract(1, "month").startOf("month"),
                            moment().subtract(1, "month").endOf("month"),
                        ],
                        [__("This Year")]: [
                            moment().startOf("year"),
                            moment().endOf("year"),
                        ],
                        [__("Last Year")]: [
                            moment().subtract(1, "year").startOf("year"),
                            moment().subtract(1, "year").endOf("year"),
                        ],
                    },
                },
                cb
            );

            // Call the callback function once to display the initial range
            cb(start, end);

            // Mark element as initialized to prevent reinitialization
            element.setAttribute("data-missions-initialized", "1");
        });
    };
    createDateMissionStatusRangePickers();
});
