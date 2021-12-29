"use strict";

import * as mysql from "promise-mysql";
import { config } from "../db/bikes.js";

let db;
let bikes;

async function connect() {
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

export {
    getBikes,
    connect
}
