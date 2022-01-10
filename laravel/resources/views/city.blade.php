@include('header')

<h1>{{ json_decode($city)->city_name }}</h1>

<div class="w-full grid grid-cols-2">
    <div id="map"></div>
    <div class="w-full grid justify-center">
        <div class="w-full grid gap-y-6 justify-center grid-rows-8 h-3/6">
            <div class="cursor-pointer w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="lediga">Lediga</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">Hyrda</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="parkerade">Parkerade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="laddas">På laddning</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="parkering">Parkering</div>
            <input id="bike_search" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="number" placeholder="cykel ID">
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="search">Sök</div>
        </div>
    </div>
</div>

<script>

const city = JSON.parse('<?= $city; ?>');
const parking = JSON.parse('<?= $parking ?>');

const freeBikesBtn = document.getElementById("lediga");
const movingBtn = document.getElementById("hyrda");
const parkingBtn = document.getElementById("parkering");
const searchBtn = document.getElementById("search");
const searchField = document.getElementById("bike_search");
const parkedBikesBtn = document.getElementById("parkerade");

const bikes = {};
// const socket = io("ws://127.0.0.1:5000");
const bikemarkers = L.layerGroup();
const parkingSpots = L.layerGroup();

const map = L.map('map', {
        preferCanvas: true,
    }).setView(city.position.split(" "), 16);

map.addLayer(bikemarkers);

let bikeData;
let trackMoving = false;
let trackFree = false;
let trackMalfuncBikes = false;
let trackParkedBikes = false;
let classListActive = ["bg-blue-500", "bg-blue-700"];
let classListHide = ["bg-blue-700", "bg-blue-500"];

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
        attribution: `&copy;
        <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
        keepBuffer: 20,
    }).addTo(map);

freeBikesBtn.addEventListener("click", plotFreeBikes);
movingBtn.addEventListener("click", plotMoving);
searchBtn.addEventListener("click", searchBike);
parkingBtn.addEventListener("click", plotParking);
parkedBikesBtn.addEventListener("click", plotParkedBikes);

const socket = (function(){
    const socket = io("ws://127.0.0.1:5000");
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
        // console.log(bikeData);
    });

    socket.on('bikestart', bike => {
        bikeData[bike.bikeId].state = "moving";
        let marker = bikes[bike.bikeId];
        marker.setStyle({ color: "#9B59B6" });
            if (trackMoving && bikeData[bike.bikeId].removed) {
                bikemarkers.addLayer(marker);
                bikeData[bike.bikeId].removed = false;
                return;
            }
            
            if (! trackMoving && ! bikeData[bike.bikeId].removed) {
                bikemarkers.removeLayer(marker);
                bikeData[bike.bikeId].removed = true;
                return;
            }
    });

    socket.on('bikestop', bike => {
        bikeData[bike.bikeId].state = bike.state;
        let marker = bikes[bike.bikeId];
        marker.setStyle({ color: "#3388ff" });
        if (trackFree === false) {
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

}());

prepParking();

function toggleButton(button, callbacks, classlist) {
        button.removeEventListener("click", callbacks[0]);
        button.addEventListener("click", callbacks[1]);
        button.classList.remove(classlist[0]);
        button.classList.add(classlist[1]);
}

function plotParkedBikes() {
    trackParked = true;
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "parked" && row[1].removed) {
            let marker = bikes[row[1].bikeId];
            // marker.setStyle({ color: "#9B59B6"});
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    toggleButton(parkedBikesBtn, [plotParkedBikes, hideParkedBikes], classListActive);
}

function hideParkedBikes() {
    trackParked = false;
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "parked" && row[1].removed === false) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.removeLayer(marker);
            row[1].removed = true;
        }
    }
    toggleButton(parkedBikesBtn, [ hideParkedBikes, plotParkedBikes ], classListHide);
}

function plotBikesOnCharge() {
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "charging" && row[1].removed) {
            let latlong = row.position.split(" ");
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
}

function hideBikesOnCharge() {
    plotParking= false;

    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "charging") {
            bikemarkers.removeLayer(bikes[row[1].bikeId]);
        }
    }
}

function hideBikesOnCharge() {

}

function prepParking() {
    parkingSpots.clearLayers();
    for (const row of parking) {
        let latlong = row.position.split(" ");
        let spot = new L.circle(latlong, {
            radius: 50,
            color: "#1abc9c",
            opacity: 0.4,
            fillOpacity: 0.3,
        });
        parkingSpots.addLayer(spot);
    }
}

function prepBikes() {
    for (const row of Object.entries(bikeData)) {
        let latlong = row[1].position.split(" ");
        let marker = new L.circle(latlong, {
            radius: 6,
            zIndexOffset: 1,
        }).bindPopup(
            `ID: ${row[1].bikeId}<br>
            Battery: ${row[1].battery}<br>
            Status: ${row[1].status}<br>
            Position: ${row[1].position}<br>
            State: ${row[1].state}`
            );

        bikes[row[1].bikeId] = marker;
        row[1].removed = true;
    }
}

function plotParking() {
    map.removeLayer(bikemarkers);
    map.addLayer(parkingSpots);
    map.addLayer(bikemarkers);

    toggleButton(parkingBtn, [ plotParking, hideParking ], classListActive);
}

function hideParking() {
    map.removeLayer(parkingSpots);
    toggleButton(parkingBtn, [ hideParking, plotParking ], classListHide);
}

function plotFreeBikes() {
    trackFree = true;
    for (const row of Object.entries(bikeData)) {
        if (row[1].removed && row[1].state === "free") {
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    toggleButton(freeBikesBtn, [ plotFreeBikes, hideFreeBikes ], classListActive);
}

function hideFreeBikes() {
    trackFree = false;
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "free") {
            let marker = bikes[row[1].bikeId];
            bikemarkers.removeLayer(marker);
            row[1].removed = true;
        }
    }
    toggleButton(freeBikesBtn, [ hideFreeBikes, plotFreeBikes ], classListHide);
}

function plotMoving() {
    trackMoving = true;
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "moving" && row[1].removed) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    toggleButton(movingBtn, [ plotMoving, hideMoving ], classListActive);
}

function hideMoving() {
    trackMoving = false;
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === "moving") {
            let marker = bikes[row[1].bikeId];
            bikemarkers.removeLayer(marker);
            row[1].removed = true;
        }
    }
    toggleButton(movingBtn, [ hideMoving, plotMoving ], classListHide);
}

function resetButtons() {
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    freeBikesBtn.classList.add("bg-blue-500");
    freeBikesBtn.classList.remove("bg-blue-700");
    parkedBikesBtn.classList.add("bg-blue-500");
    parkedBikesBtn.classList.remove("bg-blue-700");
    parkedBikesBtn.addEventListener("click", plotParkedBikes);
    freeBikesBtn.addEventListener("click", plotFreeBikes);
    movingBtn.addEventListener("click", plotMoving);
    trackFree = false;
    trackMoving = false;
    trackParked = false;
}

function searchBike() {
    resetButtons();
    let searchId = parseInt(searchField.value);
    console.log(searchId);
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
