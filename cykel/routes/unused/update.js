var database = require('../db/database');
var express = require('express');
var router = express.Router();
const ObjectId = require('mongodb').ObjectId;

router.get('/', function(req, res) {
    const data = {
        data: {
            msg: "This page wants POST, not GET."
        }
    };

    res.json(data);
});

router.post('/', async (req, res) => {
    const filter = req.body.filter; // handle POST data
    const content = {
        name: req.body.name,
        html: req.body.html
    };

    try {
        var result = await update(filter, content);

        if (result) {
            return res.json({ data: result });
        }
    } catch (e) {
        return res.status(500).json({
            errors: {
                status: 500,
                source: "/",
                title: "Database error",
                detail: e.message
            }
        });
    }
});


/**
 * Update a document.
 *
 * @async
 *
 * @param {string} filter   _id or {} to perform the operation on.
 * @param {string} content  content to update the document with.
 *
 * @throws Error when database operation fails.
 *
 * @return {resultSet} Some info, such as upsertedId and acknowledged (true/false).
 */
async function update(filter, content) {
    const db = await database.getDb();

    var fil = { "_id" : ObjectId(filter) };
    var con = { $set: content };
    //var upsert = { upsert: true };

    const resultSet = await db.collection.updateOne(fil, con);

    await db.client.close();

    return resultSet;
}

module.exports = router;
