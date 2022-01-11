const faker = require("faker");
const mysql = require("promise-mysql");

let db;
let bikes;

async function connect () {
    db = await mysql.createConnection({
        "host":     "127.0.0.1",
        "user":     "user",
        "password": "pass",
        "database": "ebike"
    });

    process.on("exit", () => {
        db.end();
    });
};
connect().then(() => {
    seedRandomUsers();
});

let city = 0;

async function seedRandomUsers() {
    let sql = "DELETE FROM `user`";
    await db.query(sql);
    for (let i = 0; i < 3000; i++) {
        // let userid = i;
        let firstName = faker.name.firstName();
        let lastName = faker.name.lastName();
        let phone = faker.phone.phoneNumber()
                    .toString()
                    .replaceAll("-", "")
                    .replaceAll(" ", "")
                    .replaceAll(".", "")
                    .replaceAll("x", "")
                    .replaceAll("(", "")
                    .replaceAll(")", "")
                    .slice(0, 10);
        let email = faker.internet.email(firstName + " " + lastName);
        // let username = faker.internet.userName(firstName + " " + lastName);
        let birth = Math.round(Math.random() * (2010 - 1940) + 1940);
        let saldo = Math.round(Math.random() * 2000);
        let payment = "Invoice";
    
        let sql = "INSERT INTO `user` VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    
        await db.query(sql, [i + 1, firstName, lastName, phone, email, birth, payment, saldo]);
    }
    process.exit();
}
