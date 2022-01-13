const map = L.map('mobile-map', {
    preferCanvas: true,
}).setView(city.position.split(" "), 16);


L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: `&copy;
    <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
    keepBuffer: 20,
}).addTo(map);
    
map.addLayer(bikeLayer);

function reCheckState() {
fetch("localhost:1337/cykel/reinit");
}