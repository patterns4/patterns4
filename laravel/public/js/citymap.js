const map = L.map('map', {
    preferCanvas: true,
}).setView(city.position.split(" "), 16);


L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
attribution: `&copy;
<a href="https://www.openstreetmap.org/copyright">
    OpenStreetMap</a> contributors`,
    keepBuffer: 20,
}).addTo(map);
    
map.addLayer(bikeLayer);

function reCheckState() {
fetch("localhost:1337/cykel/reinit");
}

map.on('click', function(e){
newSpot.remove();
var coord = e.latlng;
var lat = Math.round(coord.lat * 100000) / 100000;
var lng = Math.round(coord.lng * 100000) / 100000;
newSpot = new L.circle(coord, {
            radius: 50,
            color: "#3498DB",
            opacity: 0.4,
            fillOpacity: 0.3,
        });
map.addLayer(newSpot);
newParkingSpot.value = lat + " " + lng;
});