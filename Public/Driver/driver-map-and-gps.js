// --- SETUP MAP ---
// Create a Leaflet map and center it around Pretoria (latitude, longitude)
// 'map' refers to the <div id="map"> element in your HTML
const map = L.map('map').setView([-25.7479, 28.1888], 19);

// Add the base map tiles from OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, // How far you can zoom in
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Variables to hold route and driver information
let points = [];       // Array of GPS points (route)
let driverMarker;      // Marker showing driver's current position


// --- LOAD AND DISPLAY ROUTE POINTS ---
// This function loads the bus route from a hidden <script> tag in HTML
// Example in HTML:
// <script id="points" type="text/plain">
// lng,lat
// 28.1891,-25.7478
// 28.1900,-25.7485
// </script>
function loadRoutePoints() {
    try {
        // Get text content from the hidden script
        const csvText = document.getElementById("points").textContent.trim();

        // Split CSV lines into an array
        const lines = csvText.split("\n");

        // Convert "lng,lat" into [lat, lng] (Leaflet uses [lat, lng])
        points = lines.slice(1).map(line => {
            const [lng, lat] = line.split(",");
            return [parseFloat(lat), parseFloat(lng)];
        }).filter(p => !isNaN(p[0]) && !isNaN(p[1])); // Remove invalid points

        // --- Add circular markers for each route point ---
        points.forEach((point, index) => {
            let color = 'blue';
            let radius = 5;

            // Start and end points get special colors
            if (index === 0) { color = 'green'; radius = 6; }       // Start point
            if (index === points.length - 1) { color = 'red'; radius = 6; } // End point

            // Add small circle marker to map
            L.circleMarker(point, { color, radius, fillOpacity: 0.7 })
             .addTo(map)
             .bindPopup(`Point ${index + 1}`); // Show index on click
        });

        // --- Add the driver's bus marker ---
        const busIcon = L.icon({
            iconUrl: 'https://img.icons8.com/isometric/50/bus.png', // Bus image
            iconSize: [35, 35],   // Size of the bus image
            iconAnchor: [17, 17], // Where the image "sits" on map
            popupAnchor: [0, -20] // Where the popup appears
        });

        // Start driver marker at the first route point
        driverMarker = L.marker(points[0], { icon: busIcon })
            .addTo(map)
            .bindPopup('Your Location (Driver)');

        // Adjust map so all points are visible
        map.fitBounds(points);

    } catch (error) {
        console.error('Error loading route points:', error);
    }
}


// --- SET UP WEBSOCKET ---
// WebSocket is used to send the driver’s live GPS position to the server
// Then the server forwards it to passengers in real time
const statusEl = document.getElementById('status'); // Displays connection updates
const ws = new WebSocket("wss://tshwanebusmate.onrender.com"); // Use 'wss://' for deployed server


// --- WHEN CONNECTION OPENS ---
ws.onopen = () => {
    console.log("Connected to server");
    statusEl.textContent = "✅ Connected! Sending GPS data every 5 seconds...";
    
    // Send GPS coordinates every 5 seconds
    setInterval(() => {

        // --- Check if the browser supports GPS ---
        if (navigator.geolocation) {

            // Get current position
            navigator.geolocation.getCurrentPosition(
                position => {
                    // Create data object with location and timestamp
                    const data = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        timestamp: Date.now()
                    };
                    
                    // Move driver marker on the map
                    if (driverMarker) {
                        driverMarker.setLatLng([data.latitude, data.longitude]);
                        map.setView([data.latitude, data.longitude], 13); // Focus on driver
                    }
                    
                    // Send the location data to server if WebSocket is open
                    if (ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify(data));
                        console.log("Sent GPS:", data);
                        statusEl.textContent = `📡 Last sent: ${new Date().toLocaleTimeString()}  
                        Lat: ${data.latitude.toFixed(5)}, Lng: ${data.longitude.toFixed(5)}`;
                    }
                },
                // --- Handle GPS errors (e.g., permissions) ---
                err => {
                    console.error("Geolocation error:", err);
                    statusEl.textContent = "⚠️ Geolocation error: " + err.message;
                },
                { 
                    enableHighAccuracy: true, // More accurate readings
                    timeout: 10000,           // Wait max 10s for GPS
                    maximumAge: 0             // Don’t reuse old readings
                }
            );
        } else {
            console.error("Geolocation not supported");
            statusEl.textContent = "❌ Geolocation not supported by this browser";
        }

    }, 5000); // Every 5 seconds
};


// --- HANDLE CONNECTION CLOSURE ---
ws.onclose = () => {
    console.log("Disconnected from server");
    statusEl.textContent = "🔴 Disconnected from server";
};


// --- HANDLE WEBSOCKET ERRORS ---
ws.onerror = (error) => {
    console.error("WebSocket error:", error);
    statusEl.textContent = "⚠️ Connection error - Check if server is running";
};


// --- RUN EVERYTHING ---
loadRoutePoints();        const busIcon = L.icon({
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
const ws = new WebSocket("wss://tshwanebusmate.onrender.com"); // Change to your ngrok domain or localhost

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
