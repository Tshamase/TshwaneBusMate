//code for displaying leaflet map on driver side and sending current location to passenger side
// --- Set up Leaflet map ---
const map = L.map('map').setView([-25.7479, 28.1888], 19);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let points = [];
let driverMarker;

// --- Load and display route points ---
function loadRoutePoints() {
    try {
        // Read CSV text from the hidden <script>
        const csvText = document.getElementById("points").textContent.trim();
        const lines = csvText.split("\n");

        // Convert "lng,lat" to [lat,lng] for Leaflet
        points = lines.slice(1).map(line => {
            const [lng, lat] = line.split(",");
            return [parseFloat(lat), parseFloat(lng)];
        }).filter(p => !isNaN(p[0]) && !isNaN(p[1]));

        // --- Add circle markers for route ---
        points.forEach((point, index) => {
            let color = 'blue';
            let radius = 5;
            if (index === 0) { color = 'green'; radius = 6; }       // start
            if (index === points.length - 1) { color = 'red'; radius = 6; } // end

            L.circleMarker(point, { color, radius, fillOpacity: 0.7 })
             .addTo(map).bindPopup(`Point ${index + 1}`);
        });

        // --- Add driver/bus marker ---
        const busIcon = L.icon({
            iconUrl: 'https://img.icons8.com/isometric/50/bus.png',
            iconSize: [35, 35],
            iconAnchor: [17, 17],
            popupAnchor: [0, -20]
        });

        driverMarker = L.marker(points[0], { icon: busIcon })
            .addTo(map)
            .bindPopup('Your Location (Driver)');

        // Fit map to show all points
        map.fitBounds(points);

    } catch (error) {
        console.error('Error loading route points:', error);
    }
}

// --- WebSocket and GPS Tracking ---
const statusEl = document.getElementById('status');
const ws = new WebSocket("wss:///tshwanebusmate.onrender.com"); // Change to your ngrok domain or localhost

//connection to server.js
ws.onopen = () => {
    console.log("Connected to server");
    statusEl.textContent = "Connected! Sending GPS data every 5 seconds...";
    
    // Send GPS every 5 seconds
    setInterval(() => {
        // Get GPS location via browser
        if (navigator.geoloc ation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const data = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        timestamp: Date.now()
                    };
                    
                    // Update driver marker on map
                    if (driverMarker) {
                        driverMarker.setLatLng([data.latitude, data.longitude]);
                        map.setView([data.latitude, data.longitude], 13);
                    }
                    
                    // Send to server
                    if (ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify(data));
                        console.log("Sent GPS:", data);
                        statusEl.textContent = `Last sent: ${new Date().toLocaleTimeString()} - Lat: ${data.latitude.toFixed(5)}, Lng: ${data.longitude.toFixed(5)}`;
                    }
                },
                err => {
                    console.error("Geolocation error:", err);
                    statusEl.textContent = "Geolocation error: " + err.message;
                },
                { 
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            console.error("Geolocation not supported");
            statusEl.textContent = "Geolocation not supported by this browser";
        }
    }, 5000); // 5 seconds
};

ws.onclose = () => {
    console.log("Disconnected from server");
    statusEl.textContent = "Disconnected from server";
};

ws.onerror = (error) => {
    console.error("WebSocket error:", error);
    statusEl.textContent = "Connection error - Check if server is running";
};

// Initialize everything
loadRoutePoints();
