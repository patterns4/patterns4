/* eslint-disable */
process.env.NODE_ENV = 'test';

const { assert } = require('chai');
const chai = require('chai');
const chaiHttp = require('chai-http');
const server = require('../app.js');

let testid = "";

chai.should();

chai.use(chaiHttp);

describe('Testing API routes', () => {
    //!!!the first test resets the local test db!!!
    describe('1. /setup - drops db and inputs 3 new entries', () => {
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/setup")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    assert.equal(res.body.data.acknowledged, true);

                    done();
                });
        });
    });

    describe('2. Tests default route, returns Hello World', () => {
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("object");
                    res.body.data.msg.should.be.a("string");
                    assert.equal(res.body.data.msg, "Hello World");

                    done();
                });
        });
    });

    describe('3. Tests sub route, returns Hello World 2', () => {
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/hello")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("object");
                    res.body.data.msg.should.be.a("string");
                    assert.equal(res.body.data.msg, "Hello World 2");

                    done();
                });
        });
    });

    describe('4. Tests sub route with :msg, returns :msg', () => {
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/hello/Jon")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("object");
                    res.body.data.msg.should.be.a("string");
                    assert.equal(res.body.data.msg, "Jon");

                    done();
                });
        });
    });

    describe('5. Tests incorrect route, returns 404', () => {
        it('404 BAD PATH', (done) => {
            chai.request(server)
                .get("/wrongpath")
                .end((err, res) => {
                    res.should.have.status(404);
                    res.body.should.be.an("object");
                    res.body.errors.should.be.an("array");
                    assert.equal(res.body.errors[0].status, 404);

                    done();
                });
        });
    });

    describe('6. Tests /list route, returns an array of the db contents', () => {
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/list")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("array");
                    res.body.data.length.should.be.above(2);

                    done();
                });
        });
    });

    describe('7. Tests /create route, should input a fourth db entry', () => {
        it('500 HAPPY PATH', (done) => {
            chai.request(server)
                .post("/create")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    assert.equal(res.body.data.acknowledged, true);
                    
                    //updates the testid variable for use in the next test
                    testid = res.body.data.insertedId;

                    done();
                });
        });

        //Checks same route with get
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/create")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("object");
                    res.body.data.msg.should.be.a("string");
                    assert.equal(res.body.data.msg, "This page wants POST, not GET.");

                    done();
                });
        });
        
    });

    describe('8. Tests /update route, checks if name of the fourth entry changed (2 parts)', () => {
        //1.
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .post("/update")
                .send({
                    'filter': testid,
                    'name': "test name 2",
                    'html': "test content 2"
                })
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    assert.equal(res.body.data.acknowledged, true);

                    done();
                });
        });

        //2.
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/list")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("array");
                    res.body.data.length.should.be.above(3);
                    
                    //check that we updated the name for the fourth entry created by the previous test
                    assert.equal(res.body.data[3].name, "test name 2");

                    done();
                });
        });

        //Checks same route with get
        it('200 HAPPY PATH', (done) => {
            chai.request(server)
                .get("/update")
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.an("object");
                    res.body.data.should.be.an("object");
                    res.body.data.msg.should.be.a("string");
                    assert.equal(res.body.data.msg, "This page wants POST, not GET.");

                    done();
                });
        });
    });
});
