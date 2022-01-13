@include('header')

<h1>{{ json_decode($city)->city_name }}</h1>

<div class="w-full grid grid-cols-2">
<<<<<<< HEAD
    <div id="map"></div>
    <div class="w-full grid justify-center">
        <div class="w-full grid gap-y-6 justify-center grid-rows-8 h-3/6">
            <div class="cursor-pointer w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="lediga">Lediga</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="hyrda">Hyrda</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="parked">Parkerade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="depleted">Urladdade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="parking">Parkering</div>
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
const parkingBtn = document.getElementById("parking");
const searchBtn = document.getElementById("search");
const searchField = document.getElementById("bike_search");
const parkedBikesBtn = document.getElementById("parked");
const depletedBikesBtn = document.getElementById("depleted");
const classListActive = ["bg-blue-500", "bg-blue-700"];
const classListHide = ["bg-blue-700", "bg-blue-500"];

let bikeData;
let trackMovingBikes = false;
let trackFreeBikes = false;
let trackDepletedBikes = false;
let trackParkedBikes = false;

const bikeMarkers = {};
const bikeLayer = L.layerGroup();
const parkingSpots = L.layerGroup();

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
freeBikesBtn.addEventListener("click", plotFreeBikes);
movingBtn.addEventListener("click", plotMoving);
searchBtn.addEventListener("click", searchBike);
parkingBtn.addEventListener("click", plotParking);
parkedBikesBtn.addEventListener("click", plotParkedBikes);
depletedBikesBtn.addEventListener("click", plotDepletedBikes);

(function(){
    const socket = io("ws://localhost:5000");
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

    socket.on(`bikestart ${city.city_name}`, bike => {
        bikeData[bike.bikeId].state = "moving";
        let marker = bikeMarkers[bike.bikeId];
        marker.setStyle({ color: "#9B59B6" });
            if (trackMovingBikes && bikeData[bike.bikeId].removed) {
                bikeLayer.addLayer(marker);
                bikeData[bike.bikeId].removed = false;
                return;
            }
            
            if (! trackMovingBikes && ! bikeData[bike.bikeId].removed) {
                bikeLayer.removeLayer(marker);
                bikeData[bike.bikeId].removed = true;
                return;
            }
    });

    socket.on(`bikestop ${city.city_name}`, bike => {
        let marker = bikeMarkers[bike.bikeId];
    
        bikeData[bike.bikeId].state = bike.state;
        marker.setStyle({ color: "#3388ff" });
        marker.bindPopup(
            `ID: ${bike.bikeId}<br>
            Battery: ${bike.battery}<br>
            Status: ${bike.status}<br>
            Position: ${bike.position}<br>
            State: ${bike.state}`
            );
        if (bike.state === "free" && trackFreeBikes === false) {
            bikeLayer.removeLayer(marker);
            bikeData[bike.bikeId].removed = true;
        }
        if (bike.state === "depleted" && trackDepletedBikes === false) {
            bikeLayer.removeLayer(marker);
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
            bikeMarkers[bikeId].setLatLng(latlong);
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

function plotBikes(state) {
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === state && row[1].removed) {
            let marker = bikeMarkers[row[1].bikeId];
            bikeLayer.addLayer(marker);
            row[1].removed = false;
        }
    }
}

function hideBikes(state)  {
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === state && row[1].removed === false) {
            let marker = bikeMarkers[row[1].bikeId];
            bikeLayer.removeLayer(marker);
            row[1].removed = true;
        }
    }
}

function plotFreeBikes() {
    trackFreeBikes = true;
    plotBikes("free");
    toggleButton(freeBikesBtn, [ plotFreeBikes, hideFreeBikes ], classListActive);
}

function hideFreeBikes() {
    trackFreeBikes = false;
    hideBikes("free");
    toggleButton(freeBikesBtn, [ hideFreeBikes, plotFreeBikes ], classListHide);
}

function plotMoving() {
    trackMovingBikes = true;
    plotBikes("moving");
    toggleButton(movingBtn, [ plotMoving, hideMoving ], classListActive);
}

function hideMoving() {
    trackMovingBikes = false;
    hideBikes("moving");
    toggleButton(movingBtn, [ hideMoving, plotMoving ], classListHide);
}

function plotDepletedBikes() {
    trackDepletedBikes = true;
    plotBikes("depleted");
    toggleButton(depletedBikesBtn, [ plotDepletedBikes, hideDepletedBikes ], classListActive);
}

function hideDepletedBikes() {
    trackDepletedBikes = false;
    hideBikes("depleted");
    toggleButton(depletedBikesBtn, [ hideDepletedBikes, plotDepletedBikes ], classListHide);
}

function plotParkedBikes() {
    trackParked = true;
    plotBikes("parked");
    toggleButton(parkedBikesBtn, [plotParkedBikes, hideParkedBikes], classListActive);
}

function hideParkedBikes() {
    trackParked = false;
    hideBikes("parked");
    toggleButton(parkedBikesBtn, [ hideParkedBikes, plotParkedBikes ], classListHide);
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
        let circleColor = row[1].state === "moving" ? "#9B59B6" : "#3388ff";
        let marker = new L.circle(latlong, {
            radius: 6,
            zIndexOffset: 1,
            color: circleColor
        }).bindPopup(
            `ID: ${row[1].bikeId}<br>
            Battery: ${row[1].battery}<br>
            Status: ${row[1].status}<br>
            Position: ${row[1].position}<br>
            State: ${row[1].state}`
            );

        bikeMarkers[row[1].bikeId] = marker;
        row[1].removed = true;
    }
}

function plotParking() {
    map.removeLayer(bikeLayer);
    map.addLayer(parkingSpots);
    map.addLayer(bikeLayer);

    toggleButton(parkingBtn, [ plotParking, hideParking ], classListActive);
}

function hideParking() {
    map.removeLayer(parkingSpots);
    toggleButton(parkingBtn, [ hideParking, plotParking ], classListHide);
}

function resetButtons() {
    depletedBikesBtn.classList.add("bg-blue-500");
    depletedBikesBtn.classList.remove("bg-blue-700");
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    freeBikesBtn.classList.add("bg-blue-500");
    freeBikesBtn.classList.remove("bg-blue-700");
    parkedBikesBtn.classList.add("bg-blue-500");
    parkedBikesBtn.classList.remove("bg-blue-700");
    parkedBikesBtn.addEventListener("click", plotParkedBikes);
    freeBikesBtn.addEventListener("click", plotFreeBikes);
    movingBtn.addEventListener("click", plotMoving);
    depletedBikesBtn.addEventListener("click", plotDepletedBikes);
    trackFreeBikesBikes = false;
    trackMovingBikesBikes = false;
    trackParkedBikes = false;
    trackDepleted = false;
}

function searchBike() {
    resetButtons();
    let searchId = parseInt(searchField.value);
    console.log(searchId);
    for (const row of Object.entries(bikeData)) {
        if (row[1].bikeId === searchId) {
            if (row[1].removed === true) {
                let marker = bikeMarkers[row[1].bikeId];
                bikeLayer.addLayer(marker);
                row[1].removed = false;
            }
            continue;
        }
        let marker = bikeMarkers[row[1].bikeId];
        bikeLayer.removeLayer(marker);
        row[1].removed = true;
    }
}

</script>
=======
    <div id="map" class="mb-4"></div>
    <div class="w-full grid justify-center">
        <div class="w-full grid gap-y-6 justify-center grid-rows-8 h-3/6">
            <div class="cursor-pointer w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="lediga">Lediga</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="hyrda">Hyrda</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="parked">Parkerade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="depleted">Urladdade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="parking">Parkering</div>
            <input id="bike_search" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="number" placeholder="cykel ID">
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold px-4 rounded justify-center flex items-center" id="search">Sök</div>
            <form class="w-full" method="POST" action="addparking">
                @csrf <!-- {{ csrf_field() }} -->
                <h4>Lägg till parkeringsplats</h4>
                <input type="hidden" value={{ json_decode($city)->city_id }} name="redirect_url">
                <input type="hidden" value='{{ json_decode($city)->city_name }}' name="city_name">
                <input type="text" required class="w-auto m-4 block box-border p-4 border-2 border-purple-300 rounded" name="parking_name" placeholder="Välj namn">
                <input id="newspot" required type="text" class="w-auto m-4 block box-border p-4 border-2 border-purple-300 rounded" name="parking_position" placeholder="Välj plats på kartan">
                <button type="submit" class="w-full h-12 mr-2 block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lägg till</button>
            </form>
        </div>
    </div>
</div>
<h2>Parkeringsplatser</h2>
<table class="table-auto">
    @foreach (json_decode($parking) as $spot)
        <tr>
            <td> {{ $spot->parking_name }} </td>
            <td> {{ $spot->position }}  </td>
            <td> {{ $spot->parking_id }} </td>
            <td>
                <form method="POST" action="deleteparking">
                    @csrf <!-- {{ csrf_field() }} -->
                    <input type="hidden" name="city_id" value={{ json_decode($city)->city_id }}>
                    <input type="hidden" name="parking_id" value={{ $spot->parking_id }}>
                    <button type="submit" action>
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<script>
    const city = JSON.parse('<?= $city; ?>');
    const parking = JSON.parse('<?= $parking ?>');
    const freeBikesBtn = document.getElementById("lediga");
    const movingBtn = document.getElementById("hyrda");
    const parkingBtn = document.getElementById("parking");
    const searchBtn = document.getElementById("search");
    const searchField = document.getElementById("bike_search");
    const parkedBikesBtn = document.getElementById("parked");
    const depletedBikesBtn = document.getElementById("depleted");
    const newParkingSpot = document.getElementById("newspot");
    const classListActive = ["bg-blue-500", "bg-blue-700"];
    const classListHide = ["bg-blue-700", "bg-blue-500"];

    let bikeData;
    let trackMovingBikes = false;
    let trackFreeBikes = false;
    let trackDepletedBikes = false;
    let trackParkedBikes = false;
    let newSpot = L.marker();

    const bikeMarkers = {};
    const bikePopups = {};
    const bikeLayer = L.layerGroup();
    const parkingSpots = L.layerGroup();
</script>

<script src="{{asset('js/citymap.js')}}"></script>
<script src="{{asset('js/cityfunctions.js')}}"></script>
<script src="{{asset('js/citysocket.js')}}"></script>

<script>
    freeBikesBtn.addEventListener("click", plotFreeBikes);
    movingBtn.addEventListener("click", plotMoving);
    searchBtn.addEventListener("click", searchBike);
    parkingBtn.addEventListener("click", plotParking);
    parkedBikesBtn.addEventListener("click", plotParkedBikes);
    depletedBikesBtn.addEventListener("click", plotDepletedBikes);
    prepParking();
</script>
>>>>>>> Dockerized3
