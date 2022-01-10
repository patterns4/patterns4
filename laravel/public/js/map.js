"use strict";

const bikemarkers = new L.LayerGroup();
const parkingSpots = new L.LayerGroup();
let bikeData;

export { bikeData };

export function showParking() {
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
    map.removeLayer(bikemarkers);
    map.addLayer(parkingSpots);
    map.addLayer(bikemarkers);

    parkingBtn.removeEventListener("click", showParking);
    parkingBtn.addEventListener("click", hideParking);
    parkingBtn.classList.add("bg-blue-700");
    parkingBtn.classList.remove("bg-blue-500");
}

export function hideParking() {
    parkingSpots.clearLayers();

    parkingBtn.addEventListener("click", showParking);
    parkingBtn.removeEventListener("click", hideParking);
    parkingBtn.classList.add("bg-blue-500");
    parkingBtn.classList.remove("bg-blue-700");
}

export function prepBikes() {
    bikemarkers.clearLayers();
    for (const row of Object.entries(bikeData)) {
        let latlong = row[1].position.split(" ");
        let marker;

        // latlong = new L.LatLng(latlong[0], latlong[1]);
        marker = new L.circle(latlong, {
            radius: 6,
            zIndexOffset: 1,
        }).bindPopup(
            `ID: ${row[1].bikeId}<br>
            Battery: ${row[1].battery}<br>
            Status: ${row[1].status}<br>
            Position: ${row[1].position}`
            );
        
        bikes[row[1].bikeId] = marker;
        row[1].removed = true;
    }
    bikemarkers.setZIndex(1000);
    // return bikemarkers;
    map.addLayer(bikemarkers);
}

export function showParked() {
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

export function showMoving() {
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

export function hideParked() {
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

export function hideMoving() {
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

export function resetButtons() {
    movingBtn.classList.add("bg-blue-500");
    movingBtn.classList.remove("bg-blue-700");
    parkedBtn.classList.add("bg-blue-500");
    parkedBtn.classList.remove("bg-blue-700");
    parkedBtn.addEventListener("click", showParked);
    movingBtn.addEventListener("click", showMoving);
    trackParked = false;
    trackMoving = false;
}

export function searchBike() {
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