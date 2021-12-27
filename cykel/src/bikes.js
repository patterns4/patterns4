// const mysql  = require("promise-mysql");
// const config = require("../db/bikes.json");
// let db;
// let bikes;

// (async function() {
//     db = await mysql.createConnection(config);

//     process.on("exit", () => {
//         db.end();
//     });
//     // console.log(db);
// })();

async function getBikes () {
    let sql = `SELECT * FROM bike;`;
    bikes = await db.query(sql);

    return bikes;
}

module.exports = {
    getBikes: getBikes
}
