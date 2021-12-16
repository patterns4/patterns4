// Express server
const port = process.env.PORT || 1337;
const express = require("express");
const app = express();

const cors = require('cors');
const morgan = require('morgan');
const bodyParser = require('body-parser');

// const index = require('./routes/index');
// const hello = require('./routes/hello');
// const update = require('./routes/update');
// const create = require('./routes/create');
// const list = require('./routes/list');
// const setup = require('./routes/setup');
const cykel = require('./routes/cykel');

//socket.io
const server = require('http').createServer(app);

// don't show the log when it is test
if (process.env.NODE_ENV !== 'test') {
    // use morgan to log at command line
    app.use(morgan('combined')); // 'combined' outputs the Apache style LOGs
}

app.use(cors());
//app.use(express.json());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

app.use((req, res, next) => {
    console.log(req.method);
    console.log(req.path);
    next();
});

app.use('/cykel', cykel); // /:msg
// app.use('/', index);
// app.use('/hello', hello); // /:msg
// app.use('/update', update);
// app.use('/create', create);
// app.use('/list', list);
// app.use('/setup', setup);


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

const serv = server.listen(port, () => console.log(`Example app listening on port ${port}!`));

module.exports = serv;
