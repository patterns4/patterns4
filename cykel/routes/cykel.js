"use strict";

import { Router } from 'express';
import { myMap } from '../app.js';

const router = Router();
let bikeIdCounter = 1;

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
            // logIdCounter++;
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

router.post('/stop/', (req, res) => { //:msg
    let bikeId = parseInt(req.body.bikeId);
    let bike = myMap.get(bikeId);
    let datad = {};

    if (bike) {
        datad = {
            bike: bike,
        };
    } else {
        return "bike not found";
    }

    try {
        let result = bike.stop(datad);

        if (result) {
            // logIdCounter++;
            return res.json(datad);
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