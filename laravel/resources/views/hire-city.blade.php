<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">
                    <h1>{{ json_decode($city)->city_name }}</h1>
                    <div class="w-full">
                        <div id="mobile-map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>

const city = JSON.parse('<?= $city; ?>');

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

const map = L.map('mobile-map', {
        preferCanvas: true,
    }).setView(city.position.split(" "), 16);

    
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
    attribution: `&copy;
    <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
        keepBuffer: 20,
    }).addTo(map);
        
map.addLayer(bikeLayer);

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
        plotFreeBikes();
        plotParkedBikes();
        plotBikes("free");
        plotBikes("parked");
        //When hiring hideFreeBikes(), hideParkedBikes()
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



function hireBike(bike_id) {
    let hireData = { "bikeId": bike_id.toString(), "userId": {{ Auth::user()->user_id }} };
    fetch("http://127.0.0.1:1337/cykel/rent", {
                method: 'POST',
                body: new URLSearchParams(hireData)
            })

    searchBike(bike_id.toString());
    
}

function plotFreeBikes() {
    trackFreeBikes = true;
    plotBikes("free");
}

function plotParkedBikes() {
    trackFreeBikes = true;
    plotBikes("free");
}

function hideFreeBikes() {
    trackFreeBikes = false;
    hideBikes("free");
    toggleButton(freeBikesBtn, [ hideFreeBikes, plotFreeBikes ], classListHide);
}

function hideParkedBikes() {
    trackParked = false;
    hideBikes("parked");
    toggleButton(parkedBikesBtn, [ hideParkedBikes, plotParkedBikes ], classListHide);
}


function prepBikes() {
    for (const row of Object.entries(bikeData)) {
        let latlong = row[1].position;
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
            State: ${row[1].state}<br>
            <button class="button" type="button" onclick="hireBike(${row[1].bikeId})">Hire</button>`
            );

        bikeMarkers[row[1].bikeId] = marker;
        row[1].removed = true;
    }
}

function searchBike(searchId) {
    searchId = parseInt(searchId);
    trackFreeBikes = false;
    trackMovingBikes = true;
    trackParkedBikes = false;
    trackDepleted = false;
    for (const row of Object.entries(bikeData)) {
        if (row[1].bikeId === searchId) {
            if (row[1].removed === true) {
                row[1].removed = false;
            }
            let marker = bikeMarkers[row[1].bikeId];
            let latlong = row[1].position;
            let circleColor = row[1].state === "moving" ? "#9B59B6" : "#3388ff";
            bikeLayer.removeLayer(marker);
            marker = new L.circle(latlong, {
                radius: 6,
                zIndexOffset: 1,
                color: circleColor
            }).bindPopup(
                `ID: ${row[1].bikeId}<br>
                Battery: ${row[1].battery}<br>
                Status: ${row[1].status}<br>
                Position: ${row[1].position}<br>
                State: ${row[1].state}<br>
                <button class="button" type="button" onclick="hireBike(${row[1].bikeId})">Stop</button>`
                );
            bikeMarkers[row[1].bikeId] = marker;
            bikeLayer.addLayer(marker);
            marker.openPopup();
            continue;
        }
        let marker = bikeMarkers[row[1].bikeId];
        bikeLayer.removeLayer(marker);
        row[1].removed = true;
    }
}

</script>
</x-app-layout>
