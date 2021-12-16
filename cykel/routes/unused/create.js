var database = require('../db/database');
var express = require('express');
var router = express.Router();

router.get('/', function(req, res) {
    const data = {
        data: {
            msg: "This page wants POST, not GET."
        }
    };

    res.json(data);
});

router.post('/', async (req, res) => {
    var content = "";

    if (process.env.NODE_ENV === 'test') {
        content = {
            name: "test name 1",
            html: "test content 1",
        };
    } else {
        content = {
            name: req.body.name || "New Default Document",
            html: req.body.html || "",
        };
    }

    try {
        var result = await create(content);

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
 * Create a document.
 *
 * @async
 *
 * @param {string} content  content to update the document with.
 *
 * @throws Error when database operation fails.
 *
 * @return {resultSet} Some info, such as upsertedId and acknowledged (true/false).
 */
async function create(content) {
    const db = await database.getDb();
    var con = content;
    const resultSet = await db.collection.insertOne(con);
    await db.client.close();
    return resultSet;
}

module.exports = router;
