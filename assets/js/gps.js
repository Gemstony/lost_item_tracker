// assets/js/gps.js
let currentLat = null;
let currentLng = null;

function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }
    
    document.getElementById('locationStatus').innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Getting location...';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            currentLat = position.coords.latitude;
            currentLng = position.coords.longitude;
            
            // Set hidden form fields
            document.getElementById('gps_latitude').value = currentLat;
            document.getElementById('gps_longitude').value = currentLng;
            
            // Update status display
            document.getElementById('locationStatus').innerHTML = 
                '<span class="text-success"><i class="fas fa-check-circle"></i> Location captured: ' + 
                currentLat.toFixed(6) + ', ' + currentLng.toFixed(6) + '</span>';
            
            // Optionally reverse geocode to get address (using free API)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${currentLat}&lon=${currentLng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('found_location').value = data.display_name.substring(0, 200);
                    }
                })
                .catch(err => console.log("Reverse geocoding error:", err));
        },
        function(error) {
            let errorMsg = "Error getting location: ";
            switch(error.code) {
                case error.PERMISSION_DENIED: errorMsg += "Permission denied."; break;
                case error.POSITION_UNAVAILABLE: errorMsg += "Position unavailable."; break;
                case error.TIMEOUT: errorMsg += "Request timed out."; break;
                default: errorMsg += "Unknown error.";
            }
            document.getElementById('locationStatus').innerHTML = '<span class="text-danger">' + errorMsg + '</span>';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}