<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Bike;
use Illuminate\Support\Facades\Http;

class CitiesController extends Controller
{
    public function show()
    {
        $cities = new City();
        $data = ["cities" => $cities->all()];
        return view('cities', $data);
    }

    public function citymap($id)
    {
        $city = new City();
        $data = ["city" => json_encode($city->where("city_id", $id)->first())];

        return view('city', $data);
    }
}
