//displays bus simulation
// --- Initialize Leaflet map ---
// Create a map inside the element with id="map"
// and set the initial view to coordinates [0,0] with zoom level 13
const map = L.map('map').setView([-25.7479, 28.1888], 10);

// Add a tile layer (the actual map images)
// Here we use free OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Array to hold all the points read from CSV
let points = [];
// Variable for the moving bus marker
let bus;


// --- Load points from hidden CSV and start animation ---
function loadAndAnimate() {
    // Get the CSV text from the hidden <script id="points">
    const csvText = document.getElementById("points").textContent.trim();
    // Split the text into individual lines
    const lines = csvText.split("\n");

    // Skip the header line and convert each row "lng,lat" into [lat,lng]
    points = lines.slice(1).map(line => {
        const [lng, lat] = line.split(",");
        return [parseFloat(lat), parseFloat(lng)]; // Leaflet needs [lat,lng]
    }).filter(p => !isNaN(p[0]) && !isNaN(p[1])); // Ignore invalid rows

    // Once points are ready, set up map and bus
    setupMapAndBus();
}


// --- Add markers and bus icon ---
function setupMapAndBus() {
    // Add circle markers for every point
    points.forEach((point, index) => {
        let color = 'blue';
        let radius = 5;

        // Make the first point green (start)
        if (index === 0) { color = 'green'; radius = 4; }
        // Make the last point red (end)
        if (index === points.length - 1) { color = 'red'; radius = 4; }

        // Draw the circle marker on the map
        L.circleMarker(point, {
            color,
            radius,
            fillOpacity: 0.7
        }).addTo(map).bindPopup(`Point ${index + 1}`);
    });

    // Define a custom bus icon
    let busIcon = L.icon({
        iconUrl: 'https://img.icons8.com/isometric/50/bus.png',
        iconSize: [30, 30],   // size of icon
        iconAnchor: [10, 15], // where the "tip" of the icon is
        popupAnchor: [0, -40] // popup position relative to icon
    });

    // Add the bus marker starting at the first point
    bus = L.marker(points[0], { icon: busIcon }).addTo(map);

    // Adjust the map view so all points are visible
    map.fitBounds(points);

    // Start the animation after 1.5 seconds
    setTimeout(animate, 1500);
}

// --- Animate the bus along the points ---
let currentPoint = 0;
function animate() {
    // Stop if there aren’t enough points to animate
    if (points.length < 2) return;

    // Move to the next point (loops back at the end)
    currentPoint = (currentPoint + 1) % points.length;
    bus.setLatLng(points[currentPoint]);

    // Call this function again after 1 second
    setTimeout(animate, 1000);
}

// --- Start everything ---
loadAndAnimate();
