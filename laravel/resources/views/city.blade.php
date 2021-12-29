@include('header')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
crossorigin=""/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.min.css" 
integrity="sha512-fYyZwU1wU0QWB4Yutd/Pvhy5J1oWAwFXun1pt+Bps04WSe4Aq6tyHlT4+MHSJhD8JlLfgLuC4CbCnX5KHSjyCg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
crossorigin=""></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js" 
integrity="sha512-OFs3W4DIZ5ZkrDhBFtsCP6JXtMEDGmhl0QPlmWYBJay40TT1n3gt2Xuw8Pf/iezgW9CdabjkNChRqozl/YADmg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier-Leaflet/0.2.6/oms.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.0/socket.io.js" integrity="sha512-nYuHvSAhY5lFZ4ixSViOwsEKFvlxHMU2NHts1ILuJgOS6ptUmAGt/0i5czIgMOahKZ6JN84YFDA+mCdky7dD8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js" integrity="sha512-7oYXeK0OxTFxndh0erL8FsjGvrl2VMDor6fVqzlLGfwOQQqTbYsGPv4ZZ15QHfSk80doyaM0ZJdvkyDcVO7KFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div id="map"></div>

<script>

let socket = io("ws://127.0.0.1:5000");

socket.on('message', text => {
    console.log("message from server");
    console.log(text);
})

socket.on('bikelocation', data => {
    data = JSON.parse(data);
    drawBikes(Object.entries(data));
});

socket.on('biketravel', data => {
    bike = JSON.parse(data);
    // console.log(bike);
    moveBike(bike);
})

let city = <?= json_encode($city); ?>;
let map = L.map('map', {
    // scrollWheelZoom: false,
    // dragging: false
}).setView(city.position.split(", "), 16);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
        attribution: `&copy;
        <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`
    }).addTo(map);

let bikes = {};

// L.control.scale().addTo(map);

// var oms = new OverlappingMarkerSpiderfier(map);

// var popup = new L.Popup();
// oms.addListener('click', function(marker) {
//   popup.setContent(marker.desc);
//   popup.setLatLng(marker.getLatLng());
//   map.openPopup(popup);
// });

// oms.addListener('spiderfy', function(markers) {
//   map.closePopup();
// });

// function drawBikes(all) {
//     // map.clearLayers();
//     for (const row of all) {
//         // var datum = window.mapData[i];
//         let coord = row[1].position.split(" ");
//         var loc = new L.LatLng(coord[0], coord[1]);
//         var marker = new L.CircleMarker(loc, { radius: 6 }).bindPopup(`ID: ${row[1].bikeId}<br>Battery: ${row[1].battery}<br>Status: ${row[1].status}`);
//         bikes[row[1].bikeId] = marker;
//         map.addLayer(marker);
//         oms.addMarker(marker);  // <-- here
//     }
// }

function moveBike(bike) {
    let bikeId = bike["bikeId"];
    let latlong = bike.position.split(" ");
    window.requestAnimationFrame(() => {
        bikes[bikeId].setLatLng(latlong);
    })
}

// let bikemarkers = L.markerClusterGroup({ maxClusterRadius: 10 });
let bikemarkers = L.layerGroup();
function drawBikes(all) {
    // map.clearLayers();
    bikemarkers.clearLayers();
    for (const row of all) {
        let latlong = row[1].position.split(" ");
        let location = new L.LatLng(latlong[0], latlong[1]);

        let marker = new L.CircleMarker(location, {
            radius: 6
        }).bindPopup(`ID: ${row[1].bikeId}<br>Battery: ${row[1].battery}<br>Status: ${row[1].status}`);
        bikes[row[1].bikeId] = marker;
        bikemarkers.addLayer(marker);
        // map.addLayer(marker);
        // bikemarkers.addLayer(marker);
    }
    map.addLayer(bikemarkers);
}

</script>
