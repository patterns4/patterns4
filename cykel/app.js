// Express server
import express from "express";
import cors from 'cors';
import morgan from 'morgan';
import bodyParser from 'body-parser';
import * as router from './routes/cykel.js';
import { connect, getBikes, getParking } from "./src/bikes_db.js";
import io from './socket.js';
import Cykel from './bikeclass.js';

const port = process.env.PORT || 1337;
const app = express();
const myMap = new Map();
const socket = io.init();

// don't show the log when it is test
if (process.env.NODE_ENV !== 'test') {
    // use morgan to log at command line
    app.use(morgan('combined')); // 'combined' outputs the Apache style LOGs
}

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

app.use((req, res, next) => {
    console.log(req.method);
    console.log(req.path);
    next();
});

app.use('/cykel', router.default); // /:msg

// Add routes for 404 and error handling
// Catch 404 and forward to error handler
// Put this last
app.use((req, res, next) => {
    var err = new Error("Not Found");
    err.status = 404;
    next(err);
});

app.use((err, req, res, next) => {
    if (res.headersSent) {
        return next(err);
    }

    res.status(err.status || 500).json({
        "errors": [
            {
                "status": err.status,
                "title":  err.message,
                "detail": err.message
            }
        ]
    });
});

(async () => {
    try {
        await connect();
        let bikes = await getBikes();
        let parking = await getParking();
    
        for (const row of bikes) {
            let bike = new Cykel(row, parking);
            myMap.set(bike.bikeId, bike);
        }
    } catch (e) {
        console.log(e);
    }
})();

app.listen(port, () => {
    console.log(`Example app listening on port ${port}!`);
});

export { app, socket, myMap };

