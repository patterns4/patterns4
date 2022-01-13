<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::create([
            'city_name' => 'Karlskrona',
            'position' => "56,16 15,59"
        ]);
        City::create([
            'city_name' => 'Växjö',
            'position' => "56,88 14,77"
        ]);
    }
}
