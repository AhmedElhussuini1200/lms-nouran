var geocoder;
var googleMap;
var marker;

function myMap() {
    initMap();
}

function initMap() {
    geocoder = new google.maps.Geocoder();

    // قراءة قيم الإحداثيات أو تعيينها افتراضياً
    var lat = parseFloat($("#x_inp").val()) || 24.7136;
    var lng = parseFloat($("#y_inp").val()) || 46.6753;

    var myLatlng = { lat: lat, lng: lng };

    googleMap = new google.maps.Map(document.getElementById("googleMap"), {
        center: myLatlng,
        zoom: 12,
    });

    marker = new google.maps.Marker({
        position: myLatlng,
        map: googleMap,
        draggable: true,
    });

    // تحديث القيم عند سحب الماركر
    google.maps.event.addListener(marker, "dragend", function (evt) {
        $("#x_inp").val(evt.latLng.lat());
        $("#y_inp").val(evt.latLng.lng());
    });

    // عند الضغط على أي نقطة على الخريطة
    google.maps.event.addListener(googleMap, "click", function (event) {
        marker.setPosition(event.latLng);
        $("#x_inp").val(event.latLng.lat());
        $("#y_inp").val(event.latLng.lng());
    });
}

// 🔹 زر الحصول على الموقع الحالي
function getCurrentPos() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                $("#x_inp").val(lat);
                $("#y_inp").val(lng);

                var newPos = new google.maps.LatLng(lat, lng);
                googleMap.setCenter(newPos);
                marker.setPosition(newPos);
            },
            function (error) {
                alert("Unable to get your location!");
            }
        );
    } else {
        alert("Geolocation is not supported.");
    }
}

// 🔹 حل مشكلة الخريطة داخل المودال: إعادة تهيئة عند فتح المودال
$("#crud_modal").on("shown.bs.modal", function () {
    setTimeout(function () {
        google.maps.event.trigger(googleMap, "resize");
        var center = marker.getPosition();
        googleMap.setCenter(center);
    }, 300);
});
