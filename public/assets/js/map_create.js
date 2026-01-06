var geocoder;
var googleMap;
var marker;

function myMap() {
    // مبدئيًا خلي الإحداثيات الافتراضية أي قيمة أو خذها من input
    var lat = parseFloat($("#location_lat_inp").val()) || 30.0444; // Cairo
    var lng = parseFloat($("#location_long_inp").val()) || 31.2357;

    var myLatlng = { lat: lat, lng: lng };

    var mapProp = {
        center: myLatlng,
        zoom: 12,
    };

    googleMap = new google.maps.Map(
        document.getElementById("googleMap"),
        mapProp
    );

    marker = new google.maps.Marker({
        position: myLatlng,
        animation: google.maps.Animation.BOUNCE,
        map: googleMap,
    });

    geocoder = new google.maps.Geocoder();

    marker.addListener("click", () => {
        googleMap.setZoom(8);
        googleMap.setCenter(marker.getPosition());
    });

    // لما المستخدم يضغط على الخريطة
    googleMap.addListener("click", (mapsMouseEvent) => {
        let clickAddress = mapsMouseEvent.latLng.toJSON();
        marker.setPosition(clickAddress);

        geocoder.geocode(
            { location: marker.getPosition() },
            function (results, status) {
                if (status == "OK" && results[0]) {
                    $("#address").html(results[0].formatted_address);
                    $("#addressInput").val(results[0].formatted_address);
                } else {
                    console.log("Geocode failed: " + status);
                }
            }
        );

        $("#location_lat_inp").val(clickAddress.lat);
        $("#location_long_inp").val(clickAddress.lng);
    });
}

function getCurrentPos() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            marker.setPosition(pos);

            geocoder.geocode(
                { location: marker.getPosition() },
                function (results, status) {
                    if (status == "OK" && results[0]) {
                        $("#location_lat_inp").val(marker.getPosition().lat());
                        $("#location_long_inp").val(marker.getPosition().lng());
                        $("#address").val(results[0].formatted_address);
                        googleMap.setZoom(12);
                        googleMap.setCenter(marker.getPosition());
                    } else {
                        console.log("Geocode failed: " + status);
                    }
                }
            );
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
