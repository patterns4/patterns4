"use strict";

import { Router } from 'express';
import { createServer } from "http";
import { Server } from "socket.io";
import { connect, getBikes, getParking, logTrip, updateBike } from "../src/bikes_db.js";
import haversine from 'haversine';

const router = Router();
const httpServer = createServer();
const travelMultiplier = 0.01;
const batteryDepletion = 0.1;

const io = new Server(httpServer, {
    path: '/',
    cors: '*'
});

httpServer.listen(5000, () => {
   console.log("Websocket started at port ", 5000)
});

io.on('connection', async () => {
    parking = await getParking();
    // console.log(parking);
    for (const row of myMap) {
        // console.log(row[1].checkState());
        row[1].state = await row[1].checkState();
    }
    console.log('a user connected');
    io.emit("message", "you're connected");
    io.emit("bikelocation", JSON.stringify(Object.fromEntries(myMap)));
});

const myMap = new Map();
let bikeIdCounter = 1;
let parking;

async function bikeInit() {
    try {
        await connect();
        let bikes = await getBikes();
        parking = await getParking();
    
        for (const row of bikes) {
            let bike = new Cykel(row);
            myMap.set(bike.bikeId, bike);
        }
    } catch (e) {
        console.log(e);
    }
}

async function toLog(start_time, start_point, end_time, travel_time, end_point, user_id, bike_id, cost) {
    try {
        await logTrip(start_time, start_point, end_time, travel_time,end_point, user_id, bike_id, cost);
    } catch (e) {
        console.log(e);
    }
}

bikeInit();

router.get('/', function(req, res) {
    res.json(myMap.size);
});

router.get("/all/", function(req, res) {
    // console.log(myMap);
    res.json(Object.fromEntries(myMap));
});

//returns bike object with given bikeId
router.get('/:msg', function(req, res) {
    let cykel = parseInt(req.params.msg);
    let cykelinfo = myMap.get(cykel);

    res.json(cykelinfo.info);
});

// creates a bike using this body -> x-www-form-urlencoded format:
//
// bikeId: 1
// position: [1,1]
// speed: 0
// status: true
// battery: 50
//
// alternatively send no arguments at all, and they will be generated
router.post('/create/', (req, res) => { //:msg
    // random variables to use if no data given when constructor called
    var randombat = Math.floor((Math.random() * 100) + 1); //random 1-100
    var randomx = Math.floor((Math.random() * 5000) + 1); //random 1-100
    var randomy = Math.floor((Math.random() * 5000) + 1); //random 1-100

    let bike = (req.body.bikeId ? req.body : false);
    let datab = {};

    // create dataset for constructor call
    if (bike) {
        datab = {
            bikeId: parseInt(bike.bikeId),
            position: JSON.parse(bike.position),
            speed: parseInt(bike.speed),
            status: JSON.parse(bike.status),
            battery: parseInt(bike.battery)
        };
    } else {
        datab = {
            bikeId: bikeIdCounter,
            position: [randomx, randomy],
            speed: 0,
            status: true,
            battery: randombat
        };
    }

    // counter for generating bikeId
    bikeIdCounter++;

    // create the bike with the above data
    let bikex = new Cykel(datab);

    // add the new bike to the Map
    try {
        var result = myMap.set(bikex.bikeId, bikex);

        if (result) {
            return res.json(datab);
        }
    } catch (e) {
        return res.status(500).json({
            errors: {
                status: 500,
                source: "/",
                title: "Dictionary error",
                detail: e.message
            }
        });
    }
});


// rents a bike and moves it from its origin to a new position given
// the body -> x-www-form-urlencoded format below
//
// bikeId: 1
// userId: 2
// destination: [75,50]
//
//
// router.get('/reinit', async () => {
//     parking = await getParking();
//     for (const row of myMap) {
        // row.state = checkState();
//     }
// })

router.post('/rent/', (req, res) => { //:msg
    let bikeId = parseInt(req.body.bikeId);
    let bike = myMap.get(bikeId);
    // let destination = JSON.parse(req.body.destination);
    let userId = parseInt(req.body.userId);
    let dt = new Date();
    let datac = {};

    if (bike) {
        datac = {
            datetime: dt,
            bikeId: bike,
            userId: userId,
            // destination: destination
        };
    } else {
        return "bike not found";
    }

    try {
        let result = bike.rent(datac);

        if (result) {
            logIdCounter++;
            return res.json(datac);
        }
    } catch (e) {
        return res.status(500).json({
            errors: {
                status: 500,
                source: "/",
                title: "Dictionary error",
                detail: e.message
            }
        });
    }
});


export default router;



// class
class Cykel {
    constructor(data) {
        // supplied on creation
        this.bikeId = data.bike_id;
        this.position = data.position.split(" ").map(x => parseFloat(x));
        this.speed = data.speed;
        this.status = data.status;
        this.state = data.state;
        this.battery = data.battery;
        this.moving = false;
        this.cityName = data.city_name;
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

    checkState() {
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

            if (haversine(spot, position, {unit: 'meter'}) <= 50) {
                return "parked";
            }
        }
        return "free";
    }

    decideDestination(position) {
        function operator(n, k) {
            return [n - k, n + k][Math.round(Math.random() * 1)];
        }
        let destination = [ operator(position[0], Math.random() * travelMultiplier),
                            operator(position[1], Math.random() * travelMultiplier) ];

        return destination.map(x => Math.round((x * 100000)) / 100000);
    }

    calcCostAndLog() {
        let start_time = this.rentDateTime;
        let end_time = new Date();
        let delta_time = parseInt(Math.abs(end_time.getTime() - start_time.getTime()) / (1000));
        let per_sec = 0.08333;
        let start_fee;
                
        if (this.state == "parked") {
            start_fee = 5;
        } else if (this.state == "free") {
            start_fee = 10;
        }

        let cost = start_fee + (delta_time * per_sec);
        toLog(this.rentDateTime, this.orgPos, end_time, delta_time, this.position, this.rentedBy, this.bikeId, cost);
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
        io.emit(`bikestart ${this.cityName}`, this);
        this.travel(first, this.cityName, increment[first], Math.abs(diffArr[first]))
        .then(() => {
        this.travel(second, this.cityName, increment[second], Math.abs(diffArr[second]))
            .then(() => {
                this.moving = false;
                this.state = this.checkState();
                this.position = this.position.map(x => Math.round(x * 100000) / 100000);
                this.calcCostAndLog();
                updateBike(this.position, this.battery, this.state, this.bikeId);

                console.log(`Bike nr ${this.bikeId} has stopped at position:`);
                console.log(this.position);
                io.emit(`bikestop ${this.cityName}`, this);
            });
        })
    }

    travel(ind, cityName, increment, diff) {
        return new Promise((resolve, reject) => {
            let position = this.position;
            let count = Math.round(diff / Math.abs(increment));
            let callCount = 0;
            let bike = this;

            let intervalId = setInterval(() => {
            
                if (callCount > count || bike.battery <= 5) {
                    clearInterval(intervalId);
                    resolve();
                    return;
                }
                this.position[ind] += increment;
                bike.battery -= batteryDepletion;
                io.emit(cityName, JSON.stringify(bike));
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
}
