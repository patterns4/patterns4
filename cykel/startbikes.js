import fetch from 'node-fetch';

let intcount = 0;
let interval = setInterval(() => {
    if (intcount >= 15) {
        clearInterval(interval);
        return;
    }

    let bikeId = intcount + 1;
    // let destination = [56.1565, 15.5864];

    // if (intcount % 2 === 0) {
    //     destination[0] = destination[0] + Math.random() * 0.01;
    //     destination[1] = destination[1] + Math.random() * 0.01;
    // } else {
    //     destination[0] = destination[0] - Math.random() * 0.01;
    //     destination[1] = destination[1] - Math.random() * 0.01;
    // }

    // destination = JSON.stringify(destination);

    let userId = intcount + 1;

    let b = 1 + 1;
    
    intcount++;

    let data = {"bikeId": bikeId.toString(), "userId": userId.toString() }

    fetch("http://localhost:1337/cykel/rent", {
    method: 'POST',
    body: new URLSearchParams(data)
    })
        .then(res => res.json()).then(json => console.log(json));
}, 1000)