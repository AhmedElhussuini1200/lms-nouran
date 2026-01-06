<script>
    let imagesBasePath = "{{ asset('/storage/Images') }}";
    let locale = "{{ app()->getLocale() }}";
    let soundStatus = 'start';
</script>

<!--begin::Javascript-->
<script>
    var hostUrl = "{{ asset('assets/') }}";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
@stack('pre_scripts')
<script>
    window.isArabic = '{{ isArabic() }}';
</script>
<!--begin::Custom Javascript(used for this page only)-->

{{--  <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>  --}}
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>
<script src="{{ asset('assets/shared/js/global.js') }}"></script>
<script src="{{ asset('assets/js/global/translations.js') }}"></script>
<script src="{{ asset('assets/js/global/scripts.js') }}"></script>
<script src="{{ asset('assets/js/favicon-badge.js') }}"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const priceInputs = document.querySelectorAll("input.numeric-input");

        priceInputs.forEach(input => {
            input.addEventListener("input", function(event) {
                let value = event.target.value.replace(/,/g, ''); // Remove existing commas
                if (!isNaN(value) && value !== "") {
                    event.target.value = Number(value).toLocaleString(
                        "en-US"); // Format with commas
                } else {
                    event.target.value = "";
                }
            });

            input.addEventListener("blur", function(event) {
                let value = event.target.value.replace(/,/g, '');
                if (value !== "") {
                    let num = parseFloat(value);
                    event.target.value = num % 1 === 0 ? num.toLocaleString("en-US") : num
                        .toLocaleString("en-US", {
                            minimumFractionDigits: 2
                        });
                }
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loader = document.getElementById("loading-div");

        if (loader) {
            loader.style.opacity = "0";
            loader.style.transition = "opacity 0.3s ease";
            setTimeout(() => loader.remove(), 300);
        }
    });
</script>

@stack('scripts')
<!--end::Custom Javascript-->
<!--end::Javascript-->
