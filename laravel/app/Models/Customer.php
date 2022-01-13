<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'user';
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'birth_year',
        'payment',
        'saldo'
    ];

    public function updatecustomer()
    {
        $this->where('user_id', $_POST["user_id"])
            ->update(
                [
                'first_name' => $_POST["first_name"],
                'last_name' => $_POST["last_name"],
                'phone' => $_POST["phone"],
                'email' => $_POST["email"],
                'birth_year' => $_POST["birth_year"],
                'payment' => $_POST["payment"],
                'saldo' => $_POST["saldo"]
                ]
            );
    }

    public function deletecustomer()
    {
        $this->where('user_id', $_POST["user_id"])->delete();
    }

    public function search($query) {
        $querystr = '%' . $query . '%';
        return $this->where('user_id', intval($query))
        ->orWhere('first_name', 'like', $querystr)
        ->orWhere('last_name', 'like', $querystr)
        ->get();
    }
}
