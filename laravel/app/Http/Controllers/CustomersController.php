<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{
    public function show()
    {
        $customers = new Customer();
        $data = [];
        if (request()->query("user_search")) {
            $search = request()->query("user_search");
            $data = ["customers" => $customers->search($search)];
            return view('customers', $data);
        }
        $data = ["customers" => $customers->all()];
        return view('customers', $data);
    }

    public function showall()
    {
        $customers = new Customer();
        $data = ["customers" => $customers->all()];
        return view('customers', $data);
    }

    public function customerdetails($id)
    {
        $customer = Customer::find($id);
        $data = ["customer" => $customer];
        return view('customer', $data);
    }

    public function updatecustomer(Request $request)
    {
        $customer = new Customer();
        $customer->updatecustomer();
        return redirect('/customers/' . $request->user_id);
    }

    public function deletecustomer()
    {
        $customer = new Customer();
        $customer->deletecustomer();
        return redirect('/customers');
    }
}
