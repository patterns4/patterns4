@include('header')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
crossorigin=""/>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
crossorigin=""></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.min.css" 
integrity="sha512-fYyZwU1wU0QWB4Yutd/Pvhy5J1oWAwFXun1pt+Bps04WSe4Aq6tyHlT4+MHSJhD8JlLfgLuC4CbCnX5KHSjyCg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js" 
integrity="sha512-OFs3W4DIZ5ZkrDhBFtsCP6JXtMEDGmhl0QPlmWYBJay40TT1n3gt2Xuw8Pf/iezgW9CdabjkNChRqozl/YADmg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier-Leaflet/0.2.6/oms.min.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.0/socket.io.js" integrity="sha512-nYuHvSAhY5lFZ4ixSViOwsEKFvlxHMU2NHts1ILuJgOS6ptUmAGt/0i5czIgMOahKZ6JN84YFDA+mCdky7dD8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src='https://unpkg.com/leaflet.glmarkers@latest/dist/Leaflet.GLMarkers.js'></script>

<script src="{{asset('js/glify-browser.js')}}"></script>

<div class="w-full grid grid-cols-2">
    <div id="map"></div>

    <div class="w-full grid grid-rows-2">
        <div class="w-full text-center">
        <h2>Parkeringar</h2>
        <p>Välj lager</p>
        </div>
        <div class="w-full text-center">
        <h2>Cyklar</h2>
        <p>Välj lager</p>
        </div>
    </div>
</div>

<script>

let city = JSON.parse('<?= $city; ?>');
let socket = io("ws://127.0.0.1:5000");
let bikemarkers = L.layerGroup();

socket.on('message', text => {
    console.log("message from server");
    console.log(text);
})

socket.on('bikelocation', data => {
    data = JSON.parse(data);
    drawBikes(Object.entries(data))
});

socket.on('biketravel', data => {
    let bike = JSON.parse(data);
    moveBike(bike);
});
let bikes = {};

let map = L.map('map', {
    // renderer: L.canvas()
    preferCanvas: true,
    // scrollWheelZoom: false,
    // dragging: false
}).setView(city.position.split(", "), 16);

// let cRenderer = L.canvas({ padding: 0.5 });

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
        attribution: `&copy;
        <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
        keepBuffer: 20,
    }).addTo(map);

function moveBike(bike) {
    let bikeId = bike["bikeId"];
    let latlong = bike.position.split(" ");
    window.requestAnimationFrame(() => {
        bikes[bikeId].setLatLng(latlong);
    })
}

// let bikemarkers = L.layerGroup();

async function drawBikes(all) {
    bikemarkers.clearLayers();
    for (const row of all) {
        let latlong = row[1].position.split(" ");
        let marker;

        latlong = new L.LatLng(latlong[0], latlong[1]);
        marker = new L.circle(latlong, {
            radius: 6,
            // renderer: cRenderer
        }).bindPopup(`ID: ${row[1].bikeId}<br>Battery: ${row[1].battery}<br>Status: ${row[1].status}`);
        bikes[row[1].bikeId] = marker;
        bikemarkers.addLayer(marker);
    }
    map.addLayer(bikemarkers);
}

</script>
