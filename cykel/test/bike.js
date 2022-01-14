import chai from 'chai';
import Cykel from '../bikeclass.js';
import { connect, getParkings } from '../db/dbfunctions.js';
import haversine from 'haversine';

const assert = chai.assert;
let parking;

(async () => {
    await connect();
    parking = await getParkings();
})();

describe('Bike', function() {
    let data = {
        bike_id: 1,
        position: "59.33317 18.0711",
        speed: 5.5,
        status: "OK",
        state: "free",
        battery: 100,
        city_name: "Stockholm Central",
        }

    let bike = new Cykel(data, haversine, parking);
    describe('#Cykel()', function() {
        it('should be a Cykel', function() {
            assert.instanceOf(bike, Cykel, "bike is a Cykel");
        });
    });
    describe('#checkState(parking)', () => {
        it('should be free', () => {
            bike.state = bike.checkState(parking);
            assert.equal(bike.state, "free", "bike is free");
        })
    });
    describe('#checkState(parking)', () => {
        it ('should be parked', () => {
            bike.position = [59.3499, 18.0714];
            bike.state = bike.checkState(parking);
            assert.equal(bike.state, "parked", "bike is parked");
        })
    });
});
