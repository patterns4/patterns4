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

{{-- <script src='https://unpkg.com/leaflet.glmarkers@latest/dist/Leaflet.GLMarkers.js'></script>

<script src="{{asset('js/glify-browser.js')}}"></script> --}}

<div class="w-full grid grid-cols-2">
    <div id="map"></div>

    <div class="w-full grid justify-center">
        <div class="w-full grid gap-y-6 justify-center grid-rows-8 h-3/6">
            <div class="cursor-pointer w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="lediga">Lediga</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">Hyrda</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">Parkerade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">På laddning</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">Upptagna</div>
            <input id="bike_search" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="number" placeholder="cykel ID">
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="search">Sök</div>
        </div>
    </div>
</div>

<script>

const parkedBtn = document.getElementById("lediga");
const movingBtn = document.getElementById("hyrda");
const parkingBtn = document.getElementById("parkering");
const searchBtn = document.getElementById("search");
const searchField = document.getElementById("bike_search");
const city = JSON.parse('<?= $city; ?>');
const bikes = {};
const movingBikes = {};
const socket = io("ws://127.0.0.1:5000");
const bikemarkers = L.layerGroup();
const map = L.map('map', {
        preferCanvas: true,
    }).setView(city.position.split(" "), 16);

let bikeData;
let trackMoving = false;
let trackParked = false;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
        attribution: `&copy;
        <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
        keepBuffer: 20,
    }).addTo(map);

parkedBtn.addEventListener("click", showParked);
movingBtn.addEventListener("click", showMoving);
searchBtn.addEventListener("click", searchBike);

socket.on('message', text => {
    console.log("message from server");
    console.log(text);
});

socket.on('bikelocation', data => {
    bikeData = JSON.parse(data);
    bikeData = Object.fromEntries(
        Object.entries(bikeData).filter(x => x[1].cityName === city.city_name)
        );
    prepBikes();
});

socket.on('bikestart', bike => {
    bikeData[bike.bikeId].moving = true;
    if (trackMoving) {
        let marker = bikes[row[1].bikeId];
        bikemarker.addLayer(marker);
        bikeData[bike.bikeId].removed = false;
    }
});

socket.on('bikestop', bike => {
    bikeData[bike.bikeId].moving = false;
    if (trackParked === false) {
        let marker = bikes[bike.bikeId];
        bikemarkers.removeLayer(marker);
        bikeData[bike.bikeId].removed = true;
    }
});

socket.on(city.city_name, data => {
    let bike = JSON.parse(data);
    moveBike(bike);
});

function moveBike(bike) {
    let bikeId = bike["bikeId"];
    let latlong = bike.position.split(" ");
    window.requestAnimationFrame(() => {
        bikes[bikeId].setLatLng(latlong);
    });
}

function prepBikes() {
    bikemarkers.clearLayers();
    for (const row of Object.entries(bikeData)) {
        let latlong = row[1].position.split(" ");
        let marker;

        latlong = new L.LatLng(latlong[0], latlong[1]);
        marker = new L.circle(latlong, {
            radius: 6,
        }).bindPopup(
            `ID: ${row[1].bikeId}<br>
            Battery: ${row[1].battery}<br>
            Status: ${row[1].status}`
            );
        
        bikes[row[1].bikeId] = marker;
        row[1].removed = true;
    }
    map.addLayer(bikemarkers);
}

function showParked() {
    trackParked = true;
    for (const row of Object.entries(bikeData)) {
        if (row[1].removed && row[1].moving === false) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    parkedBtn.classList.remove("bg-blue-500");
    parkedBtn.classList.add("bg-blue-700");
    parkedBtn.removeEventListener("click", showParked);
    parkedBtn.addEventListener("click", hideParked);
}

function showMoving() {
    for (const row of Object.entries(bikeData)) {
        if (row[1].moving && row[1].removed) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    movingBtn.classList.remove("bg-blue-500");
    movingBtn.classList.add("bg-blue-700");
    movingBtn.removeEventListener("click", showMoving);
    movingBtn.addEventListener("click", hideMoving);
}

function showAll() {
    for (const row of Object.entries(bikeData)) {
        let marker = bikes[row[1].bikeId];
        if (bikes[row[1].bikeId].removed) {
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    movingBtn.removeEventListener("click", showMoving);
    movingBtn.aaEventListener("click", hideMoving);
}

function hideParked() {
    trackParked = false;
    for (const row of Object.entries(bikeData)) {
        if (row[1].moving) {
            continue
        }
        let marker = bikes[row[1].bikeId];
        bikemarkers.removeLayer(marker);
        row[1].removed = true;
    }
    parkedBtn.classList.add("bg-blue-500");
    parkedBtn.classList.remove("bg-blue-700");
    parkedBtn.removeEventListener("click", hideParked);
    parkedBtn.addEventListener("click", showParked);
}

function hideMoving() {
    for (const row of Object.entries(bikeData)) {
        if (row[1].moving) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.removeLayer(marker);
            row[1].removed = true;
        }
    }
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    movingBtn.removeEventListener("click", hideMoving);
    movingBtn.addEventListener("click", showMoving);
}

function resetButtons() {
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    parkedBtn.classList.add("bg-blue-500");
    parkedBtn.classList.remove("bg-blue-700");
    parkedBtn.addEventListener("click", showParked);
    movingBtn.addEventListener("click", showMoving);
    trackParked = false;
    trackMoving = false;
}

function searchBike() {
    resetButtons();
    let searchId = parseInt(searchField.value);
    for (const row of Object.entries(bikeData)) {
        if (row[1].bikeId === searchId) {
            if (row[1].removed === true) {
                let marker = bikes[row[1].bikeId];
                bikemarkers.addLayer(marker);
                row[1].removed = false;
            }
            continue;
        }
        let marker = bikes[row[1].bikeId];
        bikemarkers.removeLayer(marker);
        row[1].removed = true;
    }
}

</script>
