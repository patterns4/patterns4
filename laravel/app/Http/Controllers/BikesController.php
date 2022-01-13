<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bike;

class BikesController extends Controller
{
    public function show()
    {
        $bikes = new Bike();
        $data = ["bikes" => $bikes->all()];
        return view('bikes', $data);
    }
}
