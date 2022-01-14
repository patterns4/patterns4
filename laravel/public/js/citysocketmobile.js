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
});

socket.on(`bikestart ${city.city_name}`, bike => {
    moveBike(bike);
    return;
});

socket.on(`bikestop ${city.city_name}`, bike => {
    let circleColor = bike.state === "depleted" ? "#E74C3C" : "#3388ff";
    bikeData[bike.bikeId].state = bike.state;
    bikeMarkers[bike.bikeId].setStyle({ color: circleColor });
    bikePopups[bike.bikeId].setContent(`ID: ${bike.bikeId}<br>
            Battery: ${bike.battery}<br>
            Status: ${bike.status}<br>
            Position: ${bike.position}<br>
            State: ${bike.state}<br>
            <button class="button" type="button" onclick="hireBike(${bike.bikeId})">Hire</button>`);
});

socket.on(city.city_name, data => {
    let bike = JSON.parse(data);
    moveBike(bike);
});
