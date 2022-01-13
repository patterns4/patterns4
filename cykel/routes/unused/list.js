var database = require('../db/database');
var express = require('express');
var router = express.Router();

// Return a JSON object with list of all documents within the collection.
router.get("/", async (req, res) => {
    try {
        let result = await findInCollection({}, {}, 0);

        if (result) {
            return res.json({ data: result });
        }
    } catch (err) {
        res.json(err);
    }
});


/**
 * Find documents in an collection by matching search criteria.
 *
 * @async
 *
 * @param {string} dsn        DSN to connect to database.
 * @param {string} colName    Name of collection.
 * @param {object} criteria   Search criteria.
 * @param {object} projection What to project in results.
 * @param {number} limit      Limit the number of documents to retrieve.
 *
 * @throws Error when database operation fails.
 *
 * @return {Promise<array>} The resultset as an array.
 */
async function findInCollection(criteria, projection, limit) {
    const db = await database.getDb();

    const res = await db.collection.find(criteria, projection).limit(limit).toArray();

    await db.client.close();

    return res;
}

module.exports = router;
