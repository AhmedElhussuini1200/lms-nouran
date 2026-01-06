
var KTCardsWidget17 = (function () {
    var initChart = function () {
        var el = document.getElementById("kt_card_widget_17_chart");
        if (!el) return;

        var options = {
            size: el.getAttribute("data-kt-size")
                ? parseInt(el.getAttribute("data-kt-size"))
                : 150,
            lineWidth: el.getAttribute("data-kt-line")
                ? parseInt(el.getAttribute("data-kt-line"))
                : 20,
            rotate: el.getAttribute("data-kt-rotate")
                ? parseInt(el.getAttribute("data-kt-rotate"))
                : 145,
        };

        // إنشاء canvas
        var canvas = document.createElement("canvas");
        canvas.width = canvas.height = options.size;
        el.appendChild(canvas);
        var ctx = canvas.getContext("2d");

        var radius = (options.size - options.lineWidth) / 2;
        ctx.translate(options.size / 2, options.size / 2);
        ctx.rotate((-1 / 2 + options.rotate / 180) * Math.PI);

        // الدالة لرسم الدائرة
        function drawCircle(color, lineWidth, percent) {
            percent = Math.min(Math.max(0, percent || 1), 1);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2 * percent, false);
            ctx.strokeStyle = color;
            ctx.lineCap = "round";
            ctx.lineWidth = lineWidth;
            ctx.stroke();
        }

        // tooltip واحد
        const tooltip = document.createElement("div");
        tooltip.style.position = "absolute";
        tooltip.style.background = "#fff";
        tooltip.style.border = "1px solid #000";
        tooltip.style.padding = "4px 6px";
        tooltip.style.display = "none";
        document.body.appendChild(tooltip);

        // ربط الدوائر بالبيانات
        const districtsArray = Object.values(topDistricts);

        let drawnCircles = []; // نخزن info لكل دائرة (لون + نسبة + نوع + حي)

        districtsArray.forEach((district) => {
            const total =
                district.people_count +
                district.companies_count +
                district.extinguishers_count || 1;

            const stats = [
                {
                    name: "People",
                    value: district.people_count,
                    color: "#0d6efd",
                },
                {
                    name: "Companies",
                    value: district.companies_count,
                    color: "#198754",
                },
                {
                    name: "Extinguishers",
                    value: district.extinguishers_count,
                    color: "#ffc107",
                },
            ];

            stats.forEach((stat) => {
                drawCircle(stat.color, options.lineWidth, stat.value / total);
                drawnCircles.push({
                    color: stat.color,
                    percent: stat.value / total,
                    name: stat.name,
                    value: stat.value,
                    district: district.name,
                });
            });
        });

        // hover على canvas
        canvas.addEventListener("mousemove", (e) => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left - options.size / 2;
            const y = e.clientY - rect.top - options.size / 2;
            const distance = Math.sqrt(x * x + y * y);
            let hovered = false;

            drawnCircles.forEach((stat) => {
                if (
                    distance >= radius - options.lineWidth &&
                    distance <= radius + options.lineWidth
                ) {
                    // عشان simplicity، نفترض كل دائرة كاملة تغطي ال radius
                    tooltip.style.left = e.pageX + "px";
                    tooltip.style.top = e.pageY + "px";
                    tooltip.innerHTML = `${stat.district} - ${stat.name}: ${stat.value}`;
                    tooltip.style.display = "block";
                    hovered = true;
                }
            });

            if (!hovered) tooltip.style.display = "none";
        });
    };

    return {
        init: function () {
            initChart();
        },
    };
})();

// Auto-initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {

    if (
        typeof KTCardsWidget17 !== "undefined" &&
        typeof KTCardsWidget17.init === "function"
    ) {
        KTCardsWidget17.init();
    }

    if (
        typeof DashboardAmanaCharts !== "undefined" &&
        typeof DashboardAmanaCharts.init === "function"
    ) {
        DashboardAmanaCharts.init();
    }
});

// Dashboard charts for Amana (moved from Blade inline scripts)
var DashboardAmanaCharts = (function () {
    function initMissionsSimple() {
        var el = document.querySelector("#missions_chart");
        if (!el || typeof ApexCharts === "undefined") {
            return;
        }

        // Check if data exists
        if (!window.dashboardAmana || !window.dashboardAmana.missionsSimple) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var cfg = window.dashboardAmana.missionsSimple;
        var series = cfg.series || [];
        var labels = cfg.labels || [];

        // Check if all data is zero or empty
        var hasData = series.length > 0 && series.some(function (val) {
            return val > 0;
        });

        // If no data, show message
        if (!hasData) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var options = {
            chart: {
                type: "donut",
                height: 300,
            },
            series: series,
            labels: labels,
            colors: cfg.colors || ["#f59e0b", "#0ea5e9", "#16a34a"],
            legend: {
                position: "bottom",
                horizontalAlign: "center",
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0) + "%";
                },
            },
        };

        var chart = new ApexCharts(el, options);
        chart.render();
    }

    function initTopDistrictsChart() {
        var el = document.querySelector("#top_districts_chart");
        if (!el || typeof ApexCharts === "undefined") {
            return;
        }

        // Check if data exists
        if (!window.topDistricts || !window.topDistricts.chartData) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var cfg = window.topDistricts.chartData;

        // Check if labels array is empty
        if (!cfg.labels || cfg.labels.length === 0) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        // إيجاد أعلى منطقة حسب مجموع الأشخاص + الشركات + المطافئ
        var maxIndex = 0;
        var maxTotal = 0;
        for (var i = 0; i < cfg.labels.length; i++) {
            var total =
                (cfg.people[i] || 0) +
                (cfg.companies[i] || 0) +
                (cfg.extinguishers[i] || 0);
            if (total > maxTotal) {
                maxTotal = total;
                maxIndex = i;
            }
        }

        // Check if maxTotal is zero (no data)
        if (maxTotal === 0) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        // تجهيز الـ series و labels و colors للمنطقة الأعلى فقط
        var series = [
            cfg.people[maxIndex] || 0,
            cfg.companies[maxIndex] || 0,
            cfg.extinguishers[maxIndex] || 0,
        ];
        var labels = [
            cfg.labels[maxIndex] + " - People",
            cfg.labels[maxIndex] + " - Companies",
            cfg.labels[maxIndex] + " - Extinguishers"

        ];

        var colors = ["#0d6efd", "#198754", "#ffc107"]; // الأزرق، الأخضر، الأصفر

        var options = {
            chart: {
                type: "donut",
                height: 350,
            },
            series: series,
            labels: labels,
            colors: colors,
            legend: {
                position: "bottom",
            },
            dataLabels: {
                enabled: true,
                // formatter: function (val, opts) {
                //     return opts.w.globals.labels[opts.seriesIndex] + ": " + val;
                // },
            },
            tooltip: {
                y: {
                    formatter: function (val, opts) {
                        // Check if opts and required properties exist
                        if (opts && opts.w && opts.w.globals && opts.w.globals.labels && opts.seriesIndex !== undefined) {
                            return opts.w.globals.labels[opts.seriesIndex] + ": " + val;
                        }
                        // Fallback: use labels array from scope
                        if (opts && opts.seriesIndex !== undefined && labels && labels[opts.seriesIndex]) {
                            return labels[opts.seriesIndex] + ": " + val;
                        }
                        // Final fallback
                        return val;
                    },
                },
            },
        };

        var chart = new ApexCharts(el, options);
        chart.render();
    }

    function initExtinguishersStatus() {
        var el = document.querySelector("#extinguishers_status_chart");
        if (!el || typeof ApexCharts === "undefined") {
            return;
        }

        // Check if data exists
        if (!window.dashboardAmana || !window.dashboardAmana.extinguishersStatus) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var cfg = window.dashboardAmana.extinguishersStatus;
        var series = cfg.series || [];

        // Check if all data is zero or empty
        var hasData = series.length > 0 && series.some(function (val) {
            return val > 0;
        });

        // If no data, show message
        if (!hasData) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var options = {
            chart: {
                type: "donut",
                height: 300,
            },
            series: series,
            labels: cfg.labels || [],
            colors: cfg.colors || ["#f59e0b", "#0ea5e9", "#16a34a"],
            legend: {
                position: "bottom",
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0);
                },
            },
        };

        new ApexCharts(el, options).render();
    }

    function initExtinguishersByCompany() {
        var el = document.querySelector("#extinguishers_by_company_chart");
        if (!el || typeof ApexCharts === "undefined") {
            return;
        }

        // Check if data exists
        if (!window.dashboardAmana || !window.dashboardAmana.extinguishersByCompany) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var cfg = window.dashboardAmana.extinguishersByCompany;
        var data = cfg.data || [];
        var labels = cfg.labels || [];

        // Check if all data is zero or empty
        var hasData = data.length > 0 && data.some(function (val) {
            return val > 0;
        });

        // If no data, show message
        if (!hasData || labels.length === 0) {
            el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 min-h-300px"><div class="text-center"><i class="ki-outline ki-information-5 fs-3x text-muted mb-3"></i><div class="text-gray-500 fw-semibold">' + __('No data available') + '</div></div></div>';
            return;
        }

        var options = {
            chart: {
                type: "bar",
                height: 300,
            },
            series: [
                {
                    name: "Extinguishers",
                    data: data,
                },
            ],
            xaxis: {
                categories: labels,
            },
            colors: ["#0ea5e9"],
            dataLabels: {
                enabled: true,
            },
        };

        new ApexCharts(el, options).render();
    }

    return {
        init: function () {
            initMissionsSimple();
            // initMissionsStatus();
            initExtinguishersStatus();
            initExtinguishersByCompany();
            initTopDistrictsChart();
        },
    };
})();
