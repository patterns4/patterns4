class Cykel {
    constructor(data, haversine, parking, updateBike, logTrip, io) {
        this.parking = parking;
        this.updateBike = updateBike;
        this.logTrip = logTrip;
        this.haversine = haversine;
        this.bikeId = data.bike_id;
        this.position = data.position.split(" ").map(x => parseFloat(x));
        this.speed = data.speed;
        this.status = data.status;
        this.state = data.state;
        this.battery = data.battery;
        this.moving = false;
        this.cityName = data.city_name;
        this.travelMultiplier = 0.001;
        this.batteryDepletion = 0.1;
        this.io = io;
    }

    // Getter
    get info() {
        return this;
    }

    // formats datetime a bit
    dtconv(dt) {
        let datetime = dt.getFullYear() + "/" + dt.getMonth() + "/" + dt.getDate() + " - " + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();

        return datetime;
    }

    checkState(parking) {
        if (this.moving) {
            return "moving"
        }

        if (this.battery < 10) {
            return "depleted";
        }
        for (const row of parking) {
            let position = this.position;
            let spot = row.position.split(" ");

            position = {latitude: position[0], longitude: position[1]};
            spot = {latitude: spot[0], longitude: spot[1]};
            if (this.haversine(spot, position, {unit: 'meter'}) <= 50) {
                return "parked";
            }
        }
        return "free";
    }

    decideDestination(position) {
        function operator(n, k) {
            return [n - k, n + k][Math.round(Math.random() * 1)];
        }
        let destination = [ operator(position[0], Math.random() * this.travelMultiplier),
                            operator(position[1], Math.random() * this.travelMultiplier) ];

        return destination.map(x => Math.round((x * 100000)) / 100000);
    }

    calcCostAndLog() {
        let start_time = this.rentDateTime;
        let end_time = new Date();
        let delta_time = parseInt(Math.abs(end_time.getTime() - start_time.getTime()) / (1000));
        let per_sec = 0.08333;
        let start_fee = 10;
                
        if (this.state == "parked") {
            start_fee = 5;
        } else if (this.state == "free") {
            start_fee = 10;
        }

        let cost = start_fee + (delta_time * per_sec);
        let paid = false;
        this.toLog(this.rentDateTime, this.orgPos, end_time, delta_time, this.position, this.rentedBy, this.bikeId, cost, paid);
    }

    simulateTravel() {
        console.log(`Bike nr ${this.bikeId} is running`);
        let first = Math.round(Math.random());
        let second = first === 1 ? 0 : 1;
        let destination = this.decideDestination(this.position);
        let diffLat = this.position[0] - destination[0];
        let diffLong = this.position[1] - destination[1];
        let diffArr = [ diffLat, diffLong ];
        
        let increment = [
            diffLat < 0 ? 0.0001 / 2 : -0.0001 / 2,
            diffLong < 0 ? 0.00015 / 2 : -0.00015 / 2,
        ];

        this.orgPos = this.position.map(x => Math.round(x * 100000) / 100000);

        const bike = Object.assign({}, this);
        delete bike.io;
        this.io.emit(`bikestart ${this.cityName}`, bike);
        this.travel(first, this.cityName, increment[first], Math.abs(diffArr[first]), bike)
        .then(() => {
        this.travel(second, this.cityName, increment[second], Math.abs(diffArr[second]), bike)
            .then(() => {
                this.moving = false;
                bike.moving = false;
                bike.position = bike.position.map(x => Math.round(x * 100000) / 100000);
                bike.state = this.checkState(this.parking);
                this.position = bike.position;
                this.battery = bike.battery;
                this.state = bike.state;
                this.calcCostAndLog();
                this.updateBike(this.position, this.battery, this.state, this.bikeId);

                console.log(`Bike nr ${this.bikeId} has stopped in state: ${this.state}, at position:`);
                console.log(this.position);

                this.io.emit(`bikestop ${this.cityName}`, bike);
            });
        })
    }

    travel(ind, cityName, increment, diff, bike) {
        return new Promise((resolve, reject) => {
            let count = Math.round(diff / Math.abs(increment));
            let callCount = 0;

            let intervalId = setInterval(() => {
            
                if (callCount > count || this.battery <= 5) {
                    clearInterval(intervalId);
                    resolve();
                    return;
                }
                bike.position[ind] += increment;
                bike.battery -= bike.batteryDepletion;
                bike.battery = parseFloat(bike.battery.toFixed(1));
                // const bike = Object.assign({}, this);
                // delete bike.io;
                this.io.emit(cityName, JSON.stringify(bike));
                callCount += 1;

            }, 1000);
        });
    }

    // called by the rent route, provides input for the travel function
    async rent(data) {
        let userId = data.userId;
        let datetime = data.datetime;
        this.rentedBy = userId;
        this.moving = true;
        this.state = "moving";
        this.rentDateTime = datetime;
        this.rentDTString = this.dtconv(datetime);

        this.simulateTravel();

        return data;
    }

    async toLog(start_time, start_point, end_time, travel_time, end_point, user_id, bike_id, cost, paid) {
        try {
            await this.logTrip(start_time, start_point, end_time, travel_time,end_point, user_id, bike_id, cost, paid);
        } catch (e) {
            console.log(e);
        }
    } 
}

export default Cykel;
