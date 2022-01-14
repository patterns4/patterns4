<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CustomersController extends Controller
{
    public function show()
    {
        $customers = new User();
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
        $customers = new User();
        $data = ["customers" => $customers->all()];
        return view('customers', $data);
    }

    public function customerdetails($id)
    {
        $customer = User::find($id);
        $data = ["customer" => $customer];
        return view('customer', $data);
    }

    public function updatecustomer(Request $request)
    {
        $customer = new User();
        $customer->updatecustomer();
        return redirect('admin/customers/' . $request->user_id);
    }

    public function deletecustomer()
    {
        $customer = new User();
        $customer->deletecustomer();
        return redirect('admin/customers');
    }

    public function updatepayment()
    {
        $customer = new User();
        $customer->updatecustomer();
        return redirect('/settings');
    }

    public function addsaldo()
    {
        $customer = new User();
        $customer->addsaldo();
        return redirect('/settings');
    }
}
