// --- Initialize Map ---
// Create a map centered on Pretoria (latitude, longitude)
// 'map' refers to the HTML element <div id="map"></div>
const map = L.map('map').setView([-25.7479, 28.1888], 19);

// Add the OpenStreetMap tile layer (the map background)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, // How far you can zoom in
    attribution: '&copy; OpenStreetMap contributors' // Gives credit to OSM
}).addTo(map);

// --- Global Variables ---
// Will store GPS points from CSV
let points = [];
// Will hold the moving bus marker
let bus;

// --- Connect to WebSocket ---
// WebSocket is used to get real-time GPS updates for the bus.
// Use 'wss://' for secure WebSocket connection on deployed sites like Render.
// Locally, you'd use 'ws://localhost:3000'
const ws = new WebSocket("wss://tshwanebusmate.onrender.com");

// --- Function to Load Bus Route and Animate Marker ---
function loadAndAnimate() {
    try {
        // Get CSV data stored inside a hidden <script> tag in HTML.
        // For example:
        // <script id="points" type="text/plain">
        // lng,lat
        // 28.123,-25.456
        // 28.124,-25.457
        // </script>
        const csvText = document.getElementById("points").textContent.trim();

        // Split the CSV into separate lines
        const lines = csvText.split("\n");

        // Convert each line (except the header) into a [latitude, longitude] pair
        points = lines.slice(1).map(line => {
            const [lng, lat] = line.split(",");
            return [parseFloat(lat), parseFloat(lng)]; // Leaflet expects [lat, lng]
        })
        // Filter out any invalid points (e.g. empty lines)
        .filter(p => !isNaN(p[0]) && !isNaN(p[1]));

        // --- Add Small Circles for Each Point ---
        points.forEach((point, index) => {
            let color = 'blue';
            let radius = 5;

            // Make start and end points look different
            if (index === 0) { color = 'green'; radius = 4; }       // Start point
            if (index === points.length - 1) { color = 'red'; radius = 4; } // End point

            // Add the circle marker to the map
            L.circleMarker(point, {
                color,
                radius,
                fillOpacity: 0.7
            })
            .addTo(map)
            .bindPopup(`Point ${index + 1}`); // Popup when clicked
        });

        // --- Add the Bus Icon Marker ---
        const busIcon = L.icon({
            iconUrl: 'https://img.icons8.com/isometric/50/bus.png', // Bus image
            iconSize: [30, 30],   // Width and height of the icon
            iconAnchor: [10, 15], // Where the icon "sits" on the map
            popupAnchor: [0, -40] // Where popups appear relative to the icon
        });

        // Place the bus at the starting point
        bus = L.marker(points[0], { icon: busIcon }).addTo(map);

        // Adjust map zoom and position to fit all points on screen
        map.fitBounds(points);

        // --- Handle Real-Time GPS Updates ---
        // When a message is received from the WebSocket server...
        ws.onmessage = (event) => {
            try {
                // Expecting a JSON message like:
                // { "latitude": -25.754, "longitude": 28.232 }
                const data = JSON.parse(event.data);
                console.log("Received GPS:", data);

                // Move the bus marker to the new GPS coordinates
                bus.setLatLng([data.latitude, data.longitude]);

                // Optionally, recenter the map around the bus
                map.setView([data.latitude, data.longitude], 13);
            } catch (err) {
                // If something goes wrong with parsing JSON
                console.error("Bad JSON:", event.data, err);
            }
        };

    } catch (error) {
        // Catch any unexpected error (e.g., missing CSV data)
        console.error('Error loading points from CSV:', error);
    }
}

// --- Run the Function ---
loadAndAnimate();        });

        // --- Add bus marker ---
        const busIcon = L.icon({
            iconUrl: 'https://img.icons8.com/isometric/50/bus.png',
            iconSize: [30, 30],
            iconAnchor: [10, 15],
            popupAnchor: [0, -40]
        });

        bus = L.marker(points[0], { icon: busIcon }).addTo(map);

        // Fit map to show all points
        map.fitBounds(points);

        // --- Handle live GPS updates from WebSocket ---
        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                console.log("Received GPS:", data);

                // update bus marker position
                bus.setLatLng([data.latitude, data.longitude]);
                map.setView([data.latitude, data.longitude], 13);
            } catch (err) {
                console.error("Bad JSON:", event.data, err);
            }
        };

    } catch (error) {
        console.error('Error loading points from CSV:', error);
    }
}

// Start everything
loadAndAnimate();
