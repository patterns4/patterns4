/** global: URLSearchParams */

import fetch from 'node-fetch';

let intcount = 0;

console.log();
console.log("Start renting bikes in 3 seconds");
console.log();

setTimeout(startBikes, 2000);

function startBikes() {
    let interval = setInterval(() => {
        if (intcount >= 5000) {
            clearInterval(interval);
            return;
        }
    
        let bikeId = intcount + 1;
        let userId = intcount + 1;
        let data = { "bikeId": bikeId.toString(), "userId": userId.toString() }
    
        intcount++;
        if (! Math.round(Math.random() * 2)) {
            fetch("http://express:1337/cykel/rent", {
                method: 'POST',
                body: new URLSearchParams(data)
            })
        }
    }, 500);
}

