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
        let latlong = row[1].position;
        let circleColor = "#3388ff";
        if (row[1].state === "moving") {
            circleColor = "#9B59B6";
        } else if (row[1].state === "depleted") {
            circleColor = "#E74C3C";
        }
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
    trackFreeBikes = false;
    trackMovingBikes = false;
    trackParkedBikes = false;
    trackDepleted = false;
}

function searchBike() {
    resetButtons();
    let searchId = parseInt(searchField.value);

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

