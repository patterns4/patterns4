// Express server
const port = process.env.PORT || 1337;
import express from "express";
// const express = require("express");
const app = express();
// const ws = require("ws");
import cors from 'cors';
import morgan from 'morgan';
import bodyParser from 'body-parser';
// const bodyParser = import('body-parser');

// const cykel = import('./routes/cykel');
import * as router from './routes/cykel.js';

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

app.listen(port, async () => {
    console.log(`Example app listening on port ${port}!`);
});

export { app };

