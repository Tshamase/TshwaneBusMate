//uses points.csv
//setting leaflet map
const map = L.map('map').setView([-25.7479, 28.1888], 19);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', 
    {maxZoom:19,
     attribution: '&copy; OpenStreetMap contributors'    
    }).addTo(map);

//load CSV and start animation
let points = [];
let bus;

async function loadAndAnimate() {
    try {
        const response = await fetch('points.csv');
        const csvText = await response.text();
    
        Papa.parse(csvText, {
            header: true,
            complete: function(results) {
                points = results.data
                .map(row => [parseFloat(row.lat), parseFloat(row.lng)])
                .filter(point => !isNaN(point[0]) && !isNaN(point[1]));
            
            //point markers
            points.forEach((point, index) => {
                let color = 'blue';
                let radius = 5;
                if (index === 0) { color = 'green'; radius = 4; } //start point
                if (index === points.length - 1) { color = 'red'; radius = 4; } //end point
            
                L.circleMarker(point, {
                    color: color, 
                    radius: radius,
                    fillOpacity: 0.7
                    }).addTo(map).bindPopup(`Point ${index + 1}`);
            });
            
            let busIcon = L.icon({
            iconUrl: 'https://img.icons8.com/isometric/50/bus.png', //bus icon image
            iconSize: [30, 30], //size of the icon
            iconAnchor: [10, 15], //point of the icon which corresponds to marker location
            popupAnchor: [0, -40] //where the popup opens relative to iconAnchor
            });

            //bus marker
            bus = L.marker(points[0], { icon: busIcon }).addTo(map);
            map.fitBounds(points);
            
            //start animation
            setTimeout(animate, 1000);

            }
        });
    }
    catch (error) {
        document.getElementById('status').textContent = 'Could not load CSV file';
        console.error('CSV loading error:', error);
    }
}

//point-to-point animation
let currentPoint = 0;
function animate() {
    if (points.length < 2) return;
  
    currentPoint = (currentPoint + 1) % points.length;
    bus.setLatLng(points[currentPoint]);
    
    //move to next point after 1 second
    setTimeout(animate, 1000);
}

loadAndAnimate();