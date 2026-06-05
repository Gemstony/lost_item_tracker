// assets/js/gps.js
window.getCurrentLocation = function() {
    console.log("getCurrentLocation called");

    // Try to find address field by ID first
    var addressField = document.getElementById('lost_location') || document.getElementById('found_location');
    
    // If not found, try by name attribute
    if (!addressField) {
        addressField = document.querySelector('input[name="lost_location"], input[name="found_location"]');
        console.log("Trying by name, found:", addressField);
    }
    
    // If still not found, try any input with "location" in its id/name
    if (!addressField) {
        addressField = document.querySelector('input[id*="location"], input[name*="location"]');
        console.log("Trying by partial match, found:", addressField);
    }

    console.log("Final address field:", addressField);

    if (!addressField) {
        var statusSpan = document.getElementById('locationStatus');
        if (statusSpan) statusSpan.innerHTML = '<span class="text-danger">Error: Address field not found on page.</span>';
        alert("Address field missing. Please check the page HTML.");
        return;
    }
    
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }
    
    var statusSpan = document.getElementById('locationStatus');
    if (statusSpan) statusSpan.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Getting location...';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            console.log("Got coordinates:", lat, lng);
            
            var latField = document.getElementById('gps_latitude');
            var lngField = document.getElementById('gps_longitude');
            if (latField) latField.value = lat;
            if (lngField) lngField.value = lng;
            
            if (statusSpan) statusSpan.innerHTML = '<span class="text-info">Getting address...</span>';
            
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    var address = data.display_name || lat + ", " + lng;
                    if (address.length > 100) address = address.substring(0, 100) + '…';
                    addressField.value = address;
                    if (statusSpan) statusSpan.innerHTML = '<span class="text-success">Location set: ' + address + '</span>';
                    console.log("Address set:", address);
                })
                .catch(err => {
                    console.error("Reverse geocoding error:", err);
                    addressField.value = lat + ", " + lng;
                    if (statusSpan) statusSpan.innerHTML = '<span class="text-warning">Could not get address. Please edit manually.</span>';
                });
        },
        function(error) {
            var msg = "";
            switch(error.code) {
                case error.PERMISSION_DENIED: msg = "Permission denied."; break;
                case error.POSITION_UNAVAILABLE: msg = "Position unavailable."; break;
                case error.TIMEOUT: msg = "Timeout."; break;
                default: msg = "Unknown error.";
            }
            if (statusSpan) statusSpan.innerHTML = '<span class="text-danger">' + msg + '</span>';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
};