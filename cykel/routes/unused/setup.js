var database = require('../db/database');
var express = require('express');
var router = express.Router();
//const ObjectId = require('mongodb').ObjectId;

const fs = require("fs");
const path = require("path");
const docs = JSON.parse(fs.readFileSync(
    path.resolve(__dirname, "setup.json"),
    "utf8"
));



// Runs the reset function
router.get("/", async (req, res) => {
    try {
        let result = await resetCollection(docs);

        if (result) {
            return res.json({ data: result });
        }
    } catch (err) {
        res.json(err);
    }
});



/**
 * Reset a collection by removing existing content and insert a default
 * set of documents.
 *
 * @async
 *
 * @param {string} dsn     DSN to connect to database.
 * @param {string} colName Name of collection.
 * @param {string} doc     Documents to be inserted into collection.
 *
 * @throws Error when database operation fails.
 *
 * @return {Promise<void>} Void
 */
async function resetCollection(docs) {
    const db = await database.getDb();

    const resultSet = await db.collection.deleteMany();
    await db.collection.insertMany(docs);

    await db.client.close();

    return resultSet;
}


module.exports = router;
