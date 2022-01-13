<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function show()
    {
        $logs = new Log();
        $data = ["logs" => $logs->all()];
        return view('logs', $data);
    }
    public function mobileShow()
    {
        $logs = new Log();
        $logData = $logs->where("user_id", Auth::user()->user_id)->get();
        $data = [
                    "logs" => $logData,
                    "user" => Auth::user()->user_id,
                ];
        return view('history', $data);
}

public function pay($id)
{
    $log = new Log();
    $log->pay($id);
    $customer = new User();
    $customer->pay($id);
    return redirect('/mobile/history');
}
