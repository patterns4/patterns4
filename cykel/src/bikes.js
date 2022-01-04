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

async function seedBikes () {
    let sql = "DELETE FROM bike";
    await db.query(sql);

    let bike_id = 1;
    // let location = 56.165 15.591
    let position = [56.165, 15.591];
    let temp = [];
    let speed = "0";
    let battery = "100";
    let status = "0";
    let state = "OK";
    let dec = 1;

    for (let i = 0; i < 1000; i++) {
        let lat = Math.random() * 0.01;
        let long = Math.random() * 0.01;
        lat = lat.toFixed(4);
        long = long.toFixed(4);
    
        if (dec === 1) {
            temp[0] = position[0] + parseFloat(lat);
            temp[1] = position[1] + parseFloat(long);
            temp[0] = parseFloat(temp[0].toFixed(4));
            temp[1] = parseFloat(temp[1].toFixed(4));
            dec = 2;
        } else if (dec === 2) {
            temp[0] = position[0] - parseFloat(lat);
            temp[1] = position[1] - parseFloat(long);
            temp[0] = parseFloat(temp[0].toFixed(4));
            temp[1] = parseFloat(temp[1].toFixed(4));
            dec = 3;
        } else if (dec === 3) {
            temp[0] = position[0] - parseFloat(lat);
            temp[1] = position[1] + parseFloat(long);
            temp[0] = parseFloat(temp[0].toFixed(4));
            temp[1] = parseFloat(temp[1].toFixed(4));
            dec = 4;
        } else if (dec === 4) {
            temp[0] = position[0] + parseFloat(lat);
            temp[1] = position[1] - parseFloat(long);
            temp[0] = parseFloat(temp[0].toFixed(4));
            temp[1] = parseFloat(temp[1].toFixed(4));
            dec = 1;
        }

        let pos = temp[0] + " " + temp[1];
        // console.log(pos);
        let sql =  `INSERT INTO bike VALUES(?, ?, ?, ?, ?, ?)`;

        await db.query(sql, [bike_id, pos, speed, battery, status, state]);
        bike_id += 1;
    }
}

// connect().then(() => seedBikes());
// seedBikes();

export {
    getBikes,
    connect,
    seedBikes
}
