<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'birth_year',
        'payment',
        'saldo',
        'github_id',
        'fb_id'
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

    public function pay($id)
    {
        $customer = $this->select('saldo')->where("user_id", $_POST["user_id"])->get()->first();
        $saldo = $customer->saldo;
        $saldo -= $_POST["cost".$id];
        $this->where('user_id', $_POST["user_id"])
            ->update(
                [
                'saldo' => $saldo
                ]
            );
    }

    public function addsaldo()
    {
        $customer = $this->select('saldo')->where("user_id", $_POST["user_id"])->get()->first();
        $saldo = $customer->saldo;
        $saldo += $_POST["add_saldo"];
        $this->where('user_id', $_POST["user_id"])
            ->update(
                [
                'saldo' => $saldo
                ]
            );
    }
}
