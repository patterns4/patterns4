<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    use HasFactory;
    protected  $primaryKey = 'bike_id';
    protected $table = "bike";
    public $timestamps = false;
}
