import { createServer } from "http";
import { Server } from "socket.io";

const io = {
    httpServer: createServer(),
    init: function(bikes, getParkings) {

        const io = new Server(this.httpServer, {
            path: '/',
            cors: '*'
        });

        this.httpServer.listen(5000, () => {
           console.log("Websocket started at port ", 5000)
        });

        io.on('connection', async () => {
            const parking = await getParkings();
            const mapClone = new Map();

            for (const row of bikes) {
                row[1].parking = parking;
                row[1].state = await row[1].checkState(parking);
            }

            bikes.forEach(bike => { 
                // const clone = Object.assign({}, bike);
                                    // delete clone.io;
                                    // delete clone.interval;
                                    mapClone.set(bike.bikeId, bike.bikeData);
            });

            console.log('a user connected');
            io.emit("message", "you're connected");
            // console.log(bikes)
            io.emit("bikelocation", JSON.stringify(Object.fromEntries(mapClone)));
        });
        return io;
    }
}

export default io;
