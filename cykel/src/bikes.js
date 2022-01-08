"use strict";

import * as mysql from "promise-mysql";
import { config } from "../db/bikes.js";

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
    let temp = [];
    let speed = "0";
    let battery = "100";
    let status = "0";
    let state = "OK";
    let dec = 1;

    position = position.map(x => parseFloat(x));

    for (let i = 0; i < 500; i++) {
        let lat = Math.random() * 0.005;
        let long = Math.random() * 0.01;
    
        if (dec === 1) {
            temp[0] = position[0] + lat;
            temp[1] = position[1] + long;
            temp = temp.map(x => Math.round(x * 10000) / 10000);
            dec = 2;
        } else if (dec === 2) {
            temp[0] = position[0] - lat;
            temp[1] = position[1] - long;
            temp = temp.map(x => Math.round(x * 10000) / 10000);
            dec = 3;
        } else if (dec === 3) {
            temp[0] = position[0] - lat;
            temp[1] = position[1] + long;
            temp = temp.map(x => Math.round(x * 10000) / 10000);
            dec = 4;
        } else if (dec === 4) {
            temp[0] = position[0] + lat;
            temp[1] = position[1] - long;
            temp = temp.map(x => Math.round(x * 10000) / 10000);
            dec = 1;
        }
        let pos = temp.join(" ");
        let sql =  `INSERT INTO bike VALUES(?, ?, ?, ?, ?, ?, ?)`;

        await db.query(sql, [bike_id, pos, speed, battery, status, state, city]);
        bike_id += 1;
    }
    return bike_id;
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

export {
    getBikes,
    connect,
    seedBikes,
    bikesToCities,
    getCities,
}
