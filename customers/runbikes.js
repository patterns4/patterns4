import fetch from 'node-fetch';

let intcount = 0;

console.log();
console.log("Start renting bikes in 3 second");
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
        if (Math.round(Math.random() * 1)) {
            fetch("http://127.0.0.1:1337/cykel/rent", {
                method: 'POST',
                body: new URLSearchParams(data)
            })
                // .then(res => res.json())
                // .then(json => console.log(json));
        }
    }, 300);
}
