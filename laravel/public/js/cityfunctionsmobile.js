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

function hireBike(bike_id) {
    // hiredBike = bike_id;
    let hireData = {
        "bikeId": bike_id,
        "userId": user
    };
    fetch("http://127.0.0.1:1337/cykel/rent", {
        method: 'POST',
        body: new URLSearchParams(hireData)
    });

    searchBike(bike_id);
}

function stopBike(bike_id) {
    let hireData = {
        "bikeId": bike_id,
    };
    fetch("http://127.0.0.1:1337/cykel/stop", {
        method: 'POST',
        body: new URLSearchParams(hireData)
    })

    plotBikes("free");
    plotBikes("parked");
}

function plotFreeBikes() {
    trackFreeBikes = true;
    plotBikes("free");
}

function plotParkedBikes() {
    trackFreeBikes = true;
    plotBikes("parked");
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
        });
        let popup = L.popup().setContent(`ID: ${row[1].bikeId}<br>
            Battery: ${row[1].battery}<br>
            Status: ${row[1].status}<br>
            Position: ${row[1].position}<br>
            State: ${row[1].state}<br>
            <button class="button" type="button" onclick="hireBike(${row[1].bikeId})">Hire</button>`);

        bikePopups[row[1].bikeId] = popup;
        marker.bindPopup(popup);
        if (row[1].state !== "moving") {
            bikeMarkers[row[1].bikeId] = marker;
        }
        row[1].removed = true;
    }
}

function searchBike(searchId) {
    trackParkedBikes = false;
    trackFreeBikes = false;
    // console.log(bikePopups);

    for (const row of Object.entries(bikeData)) {
        if (row[1].bikeId === searchId) {
            console.log("bike")
            if (row[1].removed === true) {
                let marker = bikeMarkers[row[1].bikeId];
                bikeLayer.addLayer(marker);
                row[1].removed = false;
            }
            bikeMarkers[row[1].bikeId].setStyle({ color: "#9B59B6" });
            continue;
        }
        let marker = bikeMarkers[row[1].bikeId];
        bikeLayer.removeLayer(marker);
        row[1].removed = true;
    }
}

function plotParking() {
    map.removeLayer(bikeLayer);
    map.addLayer(parkingSpots);
    map.addLayer(bikeLayer);
}

function moveBike(bike) {
    let bikeId = bike.bikeId;
    let latlong = bike.position;

    if (bike.state === "moving") {
        window.requestAnimationFrame(() => {
            bikeMarkers[bikeId].setLatLng(latlong);
            bikePopups[bikeId].setContent(`ID: ${bike.bikeId}<br>
                Battery: ${bike.battery}<br>
                Status: ${bike.status}<br>
                Position: ${latlong[0].toFixed(5)} ${latlong[1].toFixed(5)}<br>
                State: ${bike.state}<br>
                <button class="button" type="button" onclick="stopBike(${bike.bikeId})">Stop</button>`);
        });
    } else {
        window.requestAnimationFrame(() => {
            bikeMarkers[bikeId].setLatLng(latlong);
            bikePopups[bikeId].setContent(`ID: ${bike.bikeId}<br>
            Battery: ${bike.battery.toFixed(1)}<br>
            Status: ${bike.status}<br>
            Position: ${latlong[0].toFixed(5)} ${latlong[1].toFixed(5)}<br>
            State: ${bike.state}<br>
            <button class="button" type="button" onclick="hireBike(${bike.bikeId})">Hire</button>`);
        });
    }
}
