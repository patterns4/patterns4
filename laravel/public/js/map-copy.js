"use strict";

const parkedBtn = document.getElementById("lediga");
const movingBtn = document.getElementById("hyrda");
const parkingBtn = document.getElementById("parkering");
const searchBtn = document.getElementById("search");
const searchField = document.getElementById("bike_search");

const bikes = {};
// const socket = io("ws://127.0.0.1:5000");
const bikemarkers = L.layerGroup();
const parkingSpots = L.layerGroup();
const malfunctionBikes = L.layerGroup();
const freeBikes = L.layerGroup();
const parkedBikes = L.layerGroup();
const movingBikes = L.layerGroup();
const bikeLayers = {
    "free": freeBikes,
    "parked": parkedBikes,
    "moving": movingBikes,
    "malfunction": chargeBikes,
}

// // function bikeToLayer(bike, marker) {
    
// // }

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
            Position: ${row[1].position}`
            );
        
        bikeLayers[row[1].state];
        bikes[row[1].bikeId] = marker;

        row[1].removed = true;
    }
}

const map = L.map('map', {
        preferCanvas: true,
    }).setView(city.position.split(" "), 16);

map.addLayer(bikemarkers);

let bikeData;
let trackMoving = false;
let trackParked = false;
let trackingCharged = false;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',    {
        attribution: `&copy;
        <a href="https://www.openstreetmap.org/copyright">
        OpenStreetMap</a> contributors`,
        keepBuffer: 20,
    }).addTo(map);

parkedBtn.addEventListener("click", plotParkedBikes);
movingBtn.addEventListener("click", plotMoving);
searchBtn.addEventListener("click", searchBike);
parkingBtn.addEventListener("click", plotParking);

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
        console.log(bikeData);
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

}());

prepParking();

function plotBikesOnCharge() {
    plotParking = true;
    for (const row of Object.entries(bikeData)) {
        // bikemarkers.clearLayers();
        if (row[1].state === "charging" && row[1].removed === true) {
            let latlong = row.position.split(" ");
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
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

function plotParking() {
    map.removeLayer(bikemarkers);
    map.addLayer(parkingSpots);
    map.addLayer(bikemarkers);

    parkingBtn.removeEventListener("click", plotParking);
    parkingBtn.addEventListener("click", hideParking);
    parkingBtn.classList.add("bg-blue-700");
    parkingBtn.classList.remove("bg-blue-500");
}

function hideParking() {
    map.removeLayer(parkingSpots);
    parkingBtn.addEventListener("click", plotParking);
    parkingBtn.removeEventListener("click", hideParking);
    parkingBtn.classList.add("bg-blue-500");
    parkingBtn.classList.remove("bg-blue-700");
}

function plotParkedBikes() {
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
    parkedBtn.removeEventListener("click", plotParkedBikes);
    parkedBtn.addEventListener("click", hideParked);
}

function plotMoving() {
    for (const row of Object.entries(bikeData)) {
        if (row[1].moving && row[1].removed) {
            let marker = bikes[row[1].bikeId];
            bikemarkers.addLayer(marker);
            row[1].removed = false;
        }
    }
    movingBtn.classList.remove("bg-blue-500");
    movingBtn.classList.add("bg-blue-700");
    movingBtn.removeEventListener("click", plotMoving);
    movingBtn.addEventListener("click", hideMoving);
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
    parkedBtn.addEventListener("click", plotParkedBikes);
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
    movingBtn.addEventListener("click", plotMoving);
}

function resetButtons() {
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    parkedBtn.classList.add("bg-blue-500");
    parkedBtn.classList.remove("bg-blue-700");
    parkedBtn.addEventListener("click", plotParkedBikes);
    movingBtn.addEventListener("click", plotMoving);
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