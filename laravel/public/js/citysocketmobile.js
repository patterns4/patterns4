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
    plotFreeBikes();
    plotParkedBikes();
    plotBikes("free");
    plotBikes("parked");
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