<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">

                    <h1 class="py-3 text-xl font-semibold">{{ json_decode($city)->city_name }}</h1>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

<script>

const city = JSON.parse('<?= $city; ?>');
const parking = JSON.parse('<?= $parking ?>');

let bikeData;
let trackFreeBikes = false;
let trackParkedBikes = false;
let newSpot = L.marker();

const bikeMarkers = {};
const bikePopups = {};
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

function reCheckState() {
    fetch("localhost:1337/cykel/reinit");
}

(function(){
    const socket = io("ws://localhost:5000");
    socket.on('message', text => {
        console.log("message from server");
        console.log(text);
    });

    socket.once('bikelocation', data => {
        bikeData = JSON.parse(data);
        bikeData = Object.fromEntries(
            Object.entries(bikeData).filter(x => x[1].cityName === city.city_name)
            );
        prepBikes();
    });

    socket.on(`bikestart ${city.city_name}`, bike => {
        let marker = bikeMarkers[bike.bikeId];

        bikeData[bike.bikeId].state = "moving";
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
        let circleColor = bike.state === "depleted" ? "#E74C3C" : "#3388ff";
        bikeData[bike.bikeId].state = bike.state;
        bikeMarkers[bike.bikeId].setStyle({ color: circleColor });
        bikePopups[bike.bikeId].setContent(`ID: ${bike.bikeId}<br>
               Battery: ${bike.battery.toFixed(5)}<br>
               Status: ${bike.status}<br>
               Position: ${bike.position}<br>
               State: ${bike.state}`);
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
        let bikeId = bike.bikeId;
        let latlong = bike.position;
        window.requestAnimationFrame(() => {
            bikeMarkers[bikeId].setLatLng(latlong);
            bikePopups[bikeId].setContent(`ID: ${bike.bikeId}<br>
               Battery: ${bike.battery.toFixed(1)}<br>
               Status: ${bike.status}<br>
               Position: ${latlong[0].toFixed(5)} ${latlong[1].toFixed(5)}<br>
               State: ${bike.state}`);
        });
    }

}());

prepParking();
plotFreeBikes();
plotParkedBikes();

function plotBikes(state) {
    for (const row of Object.entries(bikeData)) {
        if (row[1].state === state && row[1].removed) {
            let marker = bikeMarkers[row[1].bikeId];
            bikeLayer.addLayer(marker);
            row[1].removed = false;
        }
    }
}
function plotFreeBikes() {
    trackFreeBikes = true;
    plotBikes("free");
    toggleButton(freeBikesBtn, [ plotFreeBikes, hideFreeBikes ], classListActive);
}

function plotParkedBikes() {
    trackParked = true;
    plotBikes("parked");
    toggleButton(parkedBikesBtn, [plotParkedBikes, hideParkedBikes], classListActive);
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
        let latlong = row[1].position;
        let circleColor = "#3388ff";
        if (row[1].state === "moving") {
            circleColor = "#9B59B6";
        } else if (row[1].state === "depleted") {
            circleColor = "#E74C3C";
        }
        // let circleColor = row[1].state === "moving" ? "#9B59B6" : "#3388ff";
        let marker = new L.circle(latlong, {
            radius: 6,
            zIndexOffset: 1,
            color: circleColor
        })
        let popup = L.popup().setContent(`ID: ${row[1].bikeId}<br>
               Battery: ${row[1].battery}<br>
               Status: ${row[1].status}<br>
               Position: ${row[1].position}<br>
               State: ${row[1].state}`);

        bikePopups[row[1].bikeId] = popup;
        marker.bindPopup(popup);
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

</script>
</x-app-layout>
