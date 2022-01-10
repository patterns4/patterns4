"use strict";

export function buttons() {
    const parkedBtn = document.getElementById("lediga");
    const movingBtn = document.getElementById("hyrda");
    const parkingBtn = document.getElementById("parkering");
    const searchBtn = document.getElementById("search");
    const searchField = document.getElementById("bike_search");

    parkedBtn.addEventListener("click", showParked);
    movingBtn.addEventListener("click", showMoving);
    searchBtn.addEventListener("click", searchBike);
    parkingBtn.addEventListener("click", showParking);
};