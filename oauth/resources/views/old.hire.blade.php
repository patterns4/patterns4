<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">

                    <h1 class="py-3 text-xl font-semibold">Hiring</h1>
                    <div>
                        <label for="cities">Choose a city:</label>
                        <select class="my-3" name="cities" id="city-selector">
                            <option value=""></option>
                        </select>
                    </div>
                    <div>
                        <label for="bikes">Choose a bike: </label>
                        <select class="my-3" name="bikes" id="bike-selector">
                        </select>
                    </div>
                    <button class="button my-3">Hire</button>
                </div>
            </div>
        </div>
    </div>

<script>
    
    (function(){
        const cities = JSON.parse('<?= $cities ?>');
        const bikes = JSON.parse('<?= $bikes ?>');
        let citybikes;
        let bikedata;
        console.log(bikes);
        let citySelector = document.getElementById("city-selector");
        let bikeSelector = document.getElementById("bike-selector");

        cities.forEach((city) => {
            let option = document.createElement("option");
            option.text = city.city_name;
            option.value = city.city_name;
            citySelector.add(option);
        });

        citySelector.onchange = function() {
            socket.on('bikelocation', data => {
                bikeData = JSON.parse(data);
                bikeData = Object.fromEntries(
                    Object.entries(bikeData).filter(x => x[1].cityName === citySelector.options[citySelector.selectedIndex].value)
                    );
                console.log(bikeData);
                prepBikes();
            });
            // citybikes = [];
            // removeOptions(bikeSelector);
            // cityBikes = bikes.filter(bike => {
            //     return bike.city_name == citySelector.options[citySelector.selectedIndex].value &&
            //             bike.state == "free";
            // })
            cityBikes.forEach((bike) => {
                let option = document.createElement("option");
                option.text = bike.bike_id;
                option.value = bike.bike_id;
                bikeSelector.add(option);
            })
        };

        function removeOptions(selectElement) {
            var i, L = selectElement.options.length - 1;
                for (i = L; i >= 0; i--) {
                    selectElement.remove(i);
                }
            }


        const socket = io("ws://localhost:5000");
        socket.on('message', text => {
            console.log("message from server");
            console.log(text);
        });

        

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
        //
        // socket.on(`bikestart ${city.city_name}`, bike => {
        //     bikeData[bike.bikeId].state = "moving";
        //     let marker = bikeMarkers[bike.bikeId];
        //     marker.setStyle({ color: "#9B59B6" });
        //         if (trackMovingBikes && bikeData[bike.bikeId].removed) {
        //             bikeLayer.addLayer(marker);
        //             bikeData[bike.bikeId].removed = false;
        //             return;
        //         }
        //
        //         if (! trackMovingBikes && ! bikeData[bike.bikeId].removed) {
        //             bikeLayer.removeLayer(marker);
        //             bikeData[bike.bikeId].removed = true;
        //             return;
        //         }
        // });
        //
        // socket.on(`bikestop ${city.city_name}`, bike => {
        //     let marker = bikeMarkers[bike.bikeId];
        //
        //     bikeData[bike.bikeId].state = bike.state;
        //     marker.setStyle({ color: "#3388ff" });
        //     marker.bindPopup(
        //         `ID: ${bike.bikeId}<br>
        //         Battery: ${bike.battery}<br>
        //         Status: ${bike.status}<br>
        //         Position: ${bike.position}<br>
        //         State: ${bike.state}`
        //         );
        //     if (bike.state === "free" && trackFreeBikes === false) {
        //         bikeLayer.removeLayer(marker);
        //         bikeData[bike.bikeId].removed = true;
        //     }
        //     if (bike.state === "depleted" && trackDepletedBikes === false) {
        //         bikeLayer.removeLayer(marker);
        //         bikeData[bike.bikeId].removed = true;
        //     }
        // });
        //
        // socket.on(city.city_name, data => {
        //     let bike = JSON.parse(data);
        //     moveBike(bike);
        // });
        //
        // function moveBike(bike) {
        //     let bikeId = bike["bikeId"];
        //     let latlong = bike.position.split(" ");
        //     window.requestAnimationFrame(() => {
        //         bikeMarkers[bikeId].setLatLng(latlong);
        //     });
        // }

    }());
</script>
</x-app-layout>
