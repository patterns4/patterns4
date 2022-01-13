<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Bike;
use App\Models\Parking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class CitiesController extends Controller
{
    public function show()
    {
        $cities = new City();
        $parking = new Parking();
        $data = ["cities" => $cities->all()];
        return view('cities', $data);
    }

    public function showMobile()
    {
        $cities = new City();
        $parking = new Parking();
        $data = ["cities" => $cities->all()];
        return view('hire', $data);
    }

    public function citymap($id)
    {
        $city = City::find($id);
        $parking = new Parking();
        // $cityData = $city->where("city_id", $id)->first();
        $parkingData = $parking->where("city_name", $city->city_name)->get();

        $data = [
                    "city" => json_encode($city),
                    "parking" => json_encode($parkingData),
                ];

        return view('city', $data);
    }

    public function citymapMobile($id)
    {
        $city = new City();
        $parking = new Parking();
        $cityData = $city->where("city_id", $id)->first();
        $parkingData = $parking->where("city_name", $cityData->city_name)->get();

        $data = [
                    "city" => json_encode($cityData),
                    "user" => Auth::user()->user_id,
                ];

        return view('hire-city', $data);
    }
}
