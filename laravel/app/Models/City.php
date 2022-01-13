<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected  $primaryKey = 'city_id';
    public $timestamps = false;
    protected $table = 'city';

    public function details()
    {

    }
}
