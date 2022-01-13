import fetch from 'node-fetch';

let intcount = 0;
let interval = setInterval(() => {
    if (intcount >= 1000) {
        clearInterval(interval);
        return;
    }

    let bikeId = intcount + 1;
    let userId = intcount + 1;
    let data = { "bikeId": bikeId.toString(), "userId": userId.toString() }

    intcount++;
    fetch("http://localhost:1337/cykel/rent", {
        method: 'POST',
        body: new URLSearchParams(data)
    })
        .then(res => res.json())
        .then(json => console.log(json));
}, 100);
