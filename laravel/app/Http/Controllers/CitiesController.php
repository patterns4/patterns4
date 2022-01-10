<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Bike;
use App\Models\Parking;
use Illuminate\Support\Facades\Http;

class CitiesController extends Controller
{
    public function show()
    {
        $cities = new City();
        $parking = new Parking();
        $data = ["cities" => $cities->all()];
        return view('cities', $data);
    }

    public function citymap($id)
    {
        $city = new City();
        $parking = new Parking();
        $cityData = $city->where("city_id", $id)->first();
        // var_dump($cityData->city_name);
        $parkingData = $parking->where("city_name", $cityData->city_name)->get();
        // var_dump($parkingData[0]);

        $data = [
                    "city" => json_encode($cityData),
                    "parking" => json_encode($parkingData),
                ];

        return view('city', $data);
    }
}
