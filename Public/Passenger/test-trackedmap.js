//receives loaction from driver side displays bus movement for pasengers
// --- Set up Leaflet map ---
const map = L.map('map').setView([-25.7479, 28.1888], 19);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let points = [];
let bus;

// Connect to WebSocket (GPS updates for bus)
const ws = new WebSocket("ws://localhost:3000"); //website link or localhost

// --- Load points from hidden <script> and animate ---
function loadAndAnimate() {
    try {
        // read CSV text from the hidden <script>
        const csvText = document.getElementById("points").textContent.trim();
        const lines = csvText.split("\n");

        // convert "lng,lat" to [lat,lng] for Leaflet
        points = lines.slice(1).map(line => {
            const [lng, lat] = line.split(",");
            return [parseFloat(lat), parseFloat(lng)];
        }).filter(p => !isNaN(p[0]) && !isNaN(p[1]));

        // --- Add circle markers ---
        points.forEach((point, index) => {
            let color = 'blue';
            let radius = 5;
            if (index === 0) { color = 'green'; radius = 4; }       // start
            if (index === points.length - 1) { color = 'red'; radius = 4; } // end

            L.circleMarker(point, { color, radius, fillOpacity: 0.7 })
             .addTo(map).bindPopup(`Point ${index + 1}`);
        });

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