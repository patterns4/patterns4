"use strict";

import { Router } from 'express';
import { createServer } from "http";
import { Server } from "socket.io";
import { connect, getBikes, bikesToCities, getParking, logTrip } from "../src/bikes_db.js";
import haversine from 'haversine-distance';

const router = Router();
const httpServer = createServer()

const io = new Server(httpServer, {
    path: '/',
    cors: '*'
});

httpServer.listen(5000, () => {
   console.log("Websocket started at port ", 5000)
});

io.on('connection', () => { 
    console.log('a user connected');
    io.emit("message", "you're connected");
    io.emit("bikelocation", JSON.stringify(Object.fromEntries(myMap)));
});

const myMap = new Map();
let bikeIdCounter = 1;
let logIdCounter = 1;
let parking;

async function bikeInit() {
    try {
        await connect();
        // await bikesToCities();
        let bikes = await getBikes();
        parking = await getParking();
        // console.log(cities)
    
        for (const row of bikes) {
            let bike = new Cykel(row);
            myMap.set(bike.bikeId, bike);
        }
    } catch (e) {
        console.log(e);
    }
}

async function toLog(log_id, start_time, start_point, end_time, end_point, user_id, bike_id) {
    try {
        await connect();
        await logTrip(log_id, start_time, start_point, end_time, end_point, user_id, bike_id);
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
        this.position = data.position;
        this.speed = data.speed;
        this.status = data.status;
        this.state = data.state;
        this.battery = data.battery;
        this.moving = false;
        this.cityName = data.city_name;

        // used and filled by functions
        // todo: group these for easy clearing and logging
    
        // this.rentedBy = "";
        // this.destination = ["", ""];
        // this.currentPosition = ["", ""];
        // this.currentDistance = "";
        // this.originalDistance = "";
        // this.rentDateTime = "";
        // this.rentDTString = "";
        // this.arrivalDateTime = "";
        // this.travelTime = "";
        // this.oldPosition = ["", ""];
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

    // returns distance between x1y1 and x2y2
    distance(position, destination) {
        let p = position;
        let d = destination;
        let x2 = d[0];
        let x1 = p[0];
        let y2 = d[1];
        let y1 = p[1];

        // sqrt dx^2 + dy^2
        let distance = Math.sqrt(
            Math.abs(((x2-x1)**2)) // converts to positive number
            + 
            Math.abs(((y2-y1)**2)) // converts to positive number
        );

        return distance;
    }

    checkState() {
        if (this.battery < 10) {
            return "depleted";
        }
        for (const row of parking) {
            let bikePosition = this.position.split(" ");
            let spot = row.position.split(" ");
            
            bikePosition = { lat: bikePosition[0], lng: bikePosition[0] };
            spot = { lat: spot[0], lng: spot[1] };

            if (haversine(spot, bikePosition) <= 50) {
                return "parked";
            }
        }
        return "free";        
    }

    decideDestination(position) {
        function operator(n, k) {
            return [n - k, n + k][Math.round(Math.random() * 1)];
        }
        let destination = [ operator(position[0], Math.random() * 0.01),
                            operator(position[1], Math.random() * 0.01) ];

        return destination.map(x => Math.round((x * 100000)) / 100000);
    }

    simulateTravel() {
        console.log(`Bike nr ${this.bikeId} is running`);
        let first = Math.round(Math.random());
        let second = first === 1 ? 0 : 1;
        let position = this.position.split(" ").map(x => parseFloat(x));
        let orgPos = position;
        let destination = this.decideDestination(position);
        let diffLat = position[0] - destination[0];
        let diffLong = position[1] - destination[1];
        let diffArr = [ diffLat, diffLong ];

        let increment = [
                        diffLat < 0 ? 0.0001 / 2 : -0.0001 / 2,
                        diffLong < 0 ? 0.00015 / 2 : -0.00015 / 2,
                        ];

        this.travel(first, this.cityName, increment[first], Math.abs(diffArr[first]))
        .then(() => {
        this.travel(second, this.cityName, increment[second], Math.abs(diffArr[second]))
            .then(() => {
                console.log(`Bike nr ${this.bikeId} has stopped at position:`);
                this.position = this.position.split(" ").map(x => Math.round(x * 100000) / 100000).join(" ");
                console.log(this.position);
                this.moving = false;
                this.state = this.checkState();
                console.log(this.battery);
                io.emit("bikestop", this);
                //log
                // toLog(log_id, start_time, start_point, end_time, end_point, user_id, bike_id);
                let end_time = datetime;
                toLog(logIdCounter, this.rentDateTime, this.orgPos, end_time, this.position, this.rentedBy, this.bikeId);
            });
        })
    }

    travel(ind, cityName, increment, diff) {
        return new Promise((resolve, reject) => {
            let position = this.position.split(" ").map(x => parseFloat(x));
            let count = Math.round(diff / Math.abs(increment));
            let callCount = 0;
            let bike = this;

            io.emit("bikestart", bike);
            let intervalId = setInterval(() => {
            
                if (callCount > count || bike.battery <= 5) {
                    clearInterval(intervalId);
                    resolve();
                    return;
                }

                position[ind] += increment;
                bike.battery -= 0.1;
                console.log(bike.battery);
                bike.position = position.join(" ");
                io.emit(cityName, JSON.stringify(bike));
                callCount += 1;

            }, 1000);
        });
    }

    // called by the rent route, provides input for the travel function
    async rent(data) {
        // let position = this.position;
        // let destination = data.destination;
        let userId = data.userId;
        let datetime = data.datetime;
        // let cityName = this.bikeCity();
        // todo group these states
        this.rentedBy = userId;
        this.moving = true;
        this.state = "moving";
        this.speed = 5.55; // 20 km/h
        // this.destination = destination;
        // this.originalDistance = this.distance(position, destination);
        this.rentDateTime = datetime;
        this.rentDTString = this.dtconv(datetime);

        this.simulateTravel();

        return data;
    }
}
