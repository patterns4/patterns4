import { createServer } from "http";
import { Server } from "socket.io";
import { getParkings } from './db/dbfunctions.js';
import { myMap } from './app.js';

const io = {
    httpServer: createServer(),
    init: function() {

        const io = new Server(this.httpServer, {
            path: '/',
            cors: '*'
        });

        this.httpServer.listen(5000, () => {
           console.log("Websocket started at port ", 5000)
        });

        // return io;
        io.on('connection', async () => {
            let parking = await getParkings();
            // console.log(parking);
            for (const row of myMap) {
                // console.log(row[1].checkState());
                row[1].state = await row[1].checkState(parking);
            }
            console.log('a user connected');
            io.emit("message", "you're connected");
            io.emit("bikelocation", JSON.stringify(Object.fromEntries(myMap)));
        });
        return io;
    }
}

export default io;
