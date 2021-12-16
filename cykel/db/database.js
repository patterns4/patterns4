const mongo = require("mongodb").MongoClient;
const config = require("./config.json");
const collectionName = "crowd";

const database = {
    getDb: async function getDb () {
        let dsn = `mongodb+srv://${config.username}:${config.password}@cluster0.jz855.mongodb.net/myFirstDatabase?retryWrites=true&w=majority`;

        if (process.env.NODE_ENV === 'test') {
            dsn = "mongodb://localhost:27017/test";
        }

        const client  = await mongo.connect(dsn, {
            useNewUrlParser: true,
            useUnifiedTopology: true,
        });
        const db = await client.db();
        const collection = await db.collection(collectionName);

        return {
            collection: collection,
            client: client,
        };
    }
};

module.exports = database;


/* Might come in handy later:


mongodb+srv://${config.username}:${config.password}@cluster0.jz855.mongodb.net/myFirstDatabase?retryWrites=true&w=majority */

/* let dsn = `mongodb+srv://${config.username}:${config.password}@cluster0.hkfbt.mongodb.net/folinodocs?retryWrites=true&w=majority`;

let dsn = `mongodb://localhost:27017/mumin`; 

*/
