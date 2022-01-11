"use strict";

import * as mysql from "promise-mysql";
import { config } from "../db/bikes.js";
import haversine from 'haversine-distance';

let db;
let bikes;

async function connect () {
    db = await mysql.createConnection(config);

    process.on("exit", () => {
        db.end();
    });
};

async function getBikes () {
    let sql = `SELECT * FROM bike;`;
    bikes = await db.query(sql);

    return bikes;
}

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
        // console.log(state);
        await db.query(sql, [bike_id, pos, speed, battery, status, state, city]);
        bike_id += 1;
    }
    return bike_id;
}

async function logTrip (data) {
    let log_id = data.log_id;
    let start_time = data.start_time;
    let start_point = data.start_point;
    let end_time = data.end_time;
    let end_point = data.end_point;
    let user_id = data.user_id;
    let bike_id = data.bike_id;

    const sql =  `INSERT INTO log VALUES(?, ?, ?, ?, ?, ?, ?)`;
        
    // await db.query(sql, [log_id, start_time, start_point, end_time, end_point, user_id, bike_id]);
    await db.query(sql, [1, '9999-12-31 23:59:59', 3, '9999-12-31 23:59:59', 5, 1, 7]);

    return log_id;
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

async function getCities() {
    let sql = "SELECT * FROM city";
    return await db.query(sql);
}

async function decideStatus(cityName) {
    let sql = "SELECT * FROM parking WHERE city_name = ?";
    let res = await db.query(sql, [cityName]);
    let decider = Math.round(Math.random() * 3);

    return res;
}

async function getParking() {
    let sql = "SELECT * FROM parking";
    return await db.query(sql);
}

export {
    getBikes,
    connect,
    seedBikes,
    bikesToCities,
    getCities,
    decideStatus,
    getParking,
    logTrip
}

async function decideState(position, parking) {
    for (const row of parking) {
        let latlong = row.position.split(" ").map(x => parseFloat(x));
        let spot = { lat: latlong[0], lng: latlong[1] };
        let bikePosition = { lat: position[0], lng: position[1] };

        if (haversine(spot, bikePosition) <= 50) {
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
