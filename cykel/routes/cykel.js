var express = require('express');
var router = express.Router();



// variables
const myMap = new Map();
let bikeIdCounter = 1;



// returns size of bike collection
router.get('/', function(req, res) {
    res.json(myMap.size);

    // const data = {
    //     data: {
    //         msg: "Hello World 2"
    //     }
    // };

    // res.json(data);
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
    let destination = JSON.parse(req.body.destination);
    let userId = parseInt(req.body.userId);
    let dt = new Date();
    let datac = {};

    if (bike) {
        datac = {
            datetime: dt,
            bikeId: bike,
            userId: userId,
            destination: destination
        };
    } else {
        return "bike not found";
    }

    try {
        var result = bike.rent(datac);

        if (result) {
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


module.exports = router;



// class
class Cykel {
    constructor(data) {
        // supplied on creation
        this.bikeId = data.bikeId;
        this.position = data.position;
        this.speed = data.speed;
        this.status = data.status;
        this.battery = data.battery;
        this.moving = false;

        // used and filled by functions
        // todo: group these for easy clearing and logging
        this.rentedBy = "";
        this.destination = ["", ""];
        this.currentPosition = ["", ""];
        this.currentDistance = "";
        this.originalDistance = "";
        this.rentDateTime = "";
        this.rentDTString = "";
        this.arrivalDateTime = "";
        this.travelTime = "";
        this.oldPosition = ["", ""];
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

    // returns a new position
    incdec(position, currentposition, destination, traveltime) {
        let x2 = destination[0];
        let x1 = position[0];
        let y2 = destination[1];
        let y1 = position[1];
        let ans = [];

        // todo use real math instead
        ans[0] = ((x2 > x1) ? currentposition[0] + ((x2 - x1) / traveltime) : currentposition[0] - ((x1 - x2) / traveltime));
        ans[1] = ((y2 > y1) ? currentposition[1] + ((y2 - y1) / traveltime) : currentposition[1] - ((y1 - y2) / traveltime));

        return ans;
    }

    // steps through the journey from origin to destination, updating the object's position in 1s intervals
    travel(position, destination, speed) {
        var minThis = this;
        let distance = minThis.distance(position, destination);
        let travelTime = distance / speed;

        minThis.travelTime = distance / speed;
        minThis.currentDistance = minThis.originalDistance;
        minThis.oldPosition = minThis.position;
        minThis.currentPosition = position;
        //minThis.position = position;
        
        // repeat every second until traveltime has ended
        var callCount = 1;
        var repeater = setInterval(function () {
            if (callCount < travelTime) {
                // eliminate last remaining current distance
                // todo decrement time instead of distance
                if (minThis.currentDistance > 5.55) {
                    minThis.currentDistance -= 5.55;
                } else {
                    minThis.currentDistance = 0;
                }

                // increment or decrement xy positions depending on travel direction
                minThis.currentPosition = minThis.incdec(minThis.oldPosition, minThis.currentPosition, destination, travelTime);
                minThis.position = minThis.currentPosition;
                
                callCount += 1;
            } else {
                // cleanup after loop has finished
                // todo clean more, trigger log write
                clearInterval(repeater);
                minThis.moving = false;
                minThis.speed = 0;
                minThis.position = minThis.destination;
                minThis.currentPosition = minThis.destination;
            }
        }, 1000);

        return;
    }

    // called by the rent route, provides input for the travel function
    rent(data) {
        let position = this.position;
        let destination = data.destination;
        let userId = data.userId;
        let datetime = data.datetime;

        // todo group these states
        this.rentedBy = userId;
        this.moving = true;
        this.speed = 5.55; // 20 km/h
        this.destination = destination;
        this.originalDistance = this.distance(position, destination);
        this.rentDateTime = datetime;
        this.rentDTString = this.dtconv(datetime);

        this.travel(position, destination, this.speed);

        return data;
    }
}
