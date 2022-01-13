import chai from 'chai';
import Cykel from '../bikeclass.js';
import { connect, getParkings } from '../db/dbfunctions.js';
const assert = chai.assert;
let parking;

(async () => {
    await connect();
    parking = await getParkings();
})();

// describe('bikeCreate', () => {
//     it('blabla', () => {

//         let bike = new Cykel({
//                                 bike_id: 1,
//                                 position: "59.33317 18.0711",
//                                 speed: 5.5,
//                                 status: "OK",
//                                 state: "free",
//                                 battery: 100,
//                                 city_name: "Stockholm Central",
//         });
//         // assert.property({ tea: { green: 'matcha' }}, 'tea');
//         assert.equal(bike.bike_id, 1);
//     }
// });

describe('Bike', function() {
    let bike = new Cykel({
        bike_id: 1,
        position: "59.33317 18.0711",
        speed: 5.5,
        status: "OK",
        state: "free",
        battery: 100,
        city_name: "Stockholm Central",
    });
    describe('#Cykel()', function() {
        it('should be a Cykel', function() {
            assert.instanceOf(bike, Cykel, "bike is a Cykel");
        });
    });
    describe('#checkState(parking)', () => {
        it('should be free', () => {
            assert.equal(bike.checkState(parking), "free", "bike is free");
        })
    })
});