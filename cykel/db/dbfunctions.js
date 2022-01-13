"use strict";

import * as mysql from "promise-mysql";
import haversine from 'haversine';
import { config as dockerConfig } from "./dbconfig.docker.js";
import { config as localConfig } from "./dbconfig.js";

let config = process.env.DB_CONFIG === "docker" ? dockerConfig : localConfig;
let db;
let bikes;

async function connect () {
    db = await mysql.createConnection(config);

    process.on("exit", () => {
        db.end();
    });
}

async function getCities() {
    let sql = `SELECT * FROM city`;
    return await db.query(sql);
}

async function getCity(city) {
    let sql = `SELECT * FROM city WHERE city_id = ` + city + ';';
    return await db.query(sql);
}

async function getBikes() {
    let sql = `SELECT * FROM bike;`;
    return await db.query(sql);
}

async function getBike(bike) {
    let sql = `SELECT * FROM bike WHERE bike_id = ` + bike + ';';
    return await db.query(sql);
}

async function getStations() {
    let sql = `SELECT * FROM station;`;
    return await db.query(sql);
}

async function getStation(station) {
    let sql = `SELECT * FROM station WHERE station_id = ` + station + ';';
    return await db.query(sql);
}

async function getParkings() {
    let sql = `SELECT * FROM parking;`;
    return await db.query(sql);
}

async function getParking(parking) {
    let sql = `SELECT * FROM parking WHERE parking_id = ?;`;
    return await db.query(sql, [parking]);
}

async function getLogs() {
    let sql = `SELECT * FROM log;`;
    return await db.query(sql);
}

async function getLog(log) {
    let sql = `SELECT * FROM log WHERE log_id = ` + log + ';';
    return await db.query(sql);
}


// async function getBikes () {
//     let sql = `SELECT * FROM bike;`;
//     bikes = await db.query(sql);

//     return bikes;
// }

async function seedBikes (position, n, city) {
    let bike_id = n;
    let speed = "0";
    let battery = "100";
    let status = "0";
    let sql = "SELECT * FROM parking";
    let dec = 1;
    let parking = await db.query(sql);

    position = position.map(x => parseFloat(x));

    for (let i = 0; i < 500; i++) {
        const res = decidePosition(position, dec);
        const state = await decideState(res[0], parking);
        const temp = res[0];
        const pos = temp.join(" ");
        const sql =  `INSERT INTO bike VALUES(?, ?, ?, ?, ?, ?, ?)`;
        
        dec = res[1];
        await db.query(sql, [bike_id, pos, speed, battery, status, state, city]);
        bike_id += 1;
    }
    return bike_id;
}

async function logTrip (start_time, start_point, end_time, travel_time, end_point, user_id, bike_id, cost, paid)  {
    const sql = `INSERT INTO log(start_time, start_point, end_time, travel_time, end_point, user_id, bike_id, cost, paid) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)`;
    start_point = start_point.join(" ");
    end_point = end_point.join(" ");
    
    await db.query(sql, [start_time, start_point, end_time, travel_time, end_point, user_id, bike_id, cost, paid]);
}

async function updateBike(position, battery, state, bikeId) {
    position = position.join(" ");
    let sql = `UPDATE bike  SET     position = ?,
                                    battery  = ?, 
                                    state    = ?
                            WHERE   bike_id  = ?`;
    await db.query(sql, [position, battery, state, bikeId]);
}

async function bikesToCities() {
    let sql = "DELETE FROM bike";
    await db.query(sql);

    sql = "SELECT * FROM city";
    let cities = await db.query(sql);

    let n = 1;
    for (const row of cities) {
        console.log(row.city_name);
        console.log(n);

        n = await seedBikes(row.position.split(" "), n, row.city_name);
    }
    console.log("Cyklar klara");
}

// async function getCities() {
//     let sql = "SELECT * FROM city";
//     return await db.query(sql);
// }

async function decideStatus(cityName) {
    let sql = "SELECT * FROM parking WHERE city_name = ?";
    let res = await db.query(sql, [cityName]);
    let decider = Math.round(Math.random() * 3);

    return res;
}

// async function getParking() {
//     let sql = "SELECT * FROM parking";
//     return await db.query(sql);
// }

export {
    getCities,
    getCity,
    getBikes,
    getBike,
    getStations,
    getStation,
    getParkings,
    getParking,
    getLogs,
    getLog,

    connect,
    seedBikes,
    bikesToCities,
    decideStatus,
    logTrip,
    updateBike
}

async function decideState(position, parking) {
    for (const row of parking) {
        let latlong = row.position.split(" ").map(x => parseFloat(x));
        let spot = { latitude: latlong[0], longitude: latlong[1] };
        let bikePosition = { latitude: position[0], longitude: position[1] };

        if (haversine(spot, bikePosition, {unit: 'meter'}) <= 50) {
            return "parked";
        }
    }
    return "free";
}

function decidePosition(position, dec) {
    let lat = Math.random() * 0.005;
    let long = Math.random() * 0.01;
    let temp = [];

    if (dec === 1) {
        temp[0] = position[0] + lat;
        temp[1] = position[1] + long;
        temp = temp.map(x => Math.round(x * 100000) / 100000);
        dec = 2;
    } else if (dec === 2) {
        temp[0] = position[0] - lat;
        temp[1] = position[1] - long;
        temp = temp.map(x => Math.round(x * 100000) / 100000);
        dec = 3;
    } else if (dec === 3) {
        temp[0] = position[0] - lat;
        temp[1] = position[1] + long;
        temp = temp.map(x => Math.round(x * 100000) / 100000);
        dec = 4;
    } else if (dec === 4) {
        temp[0] = position[0] + lat;
        temp[1] = position[1] - long;
        temp = temp.map(x => Math.round(x * 100000) / 100000);
        dec = 1;
    }
    return [temp, dec];
}
