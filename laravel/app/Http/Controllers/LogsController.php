<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function show()
    {
        $logs = new Log();
        $logData = $logs->where("user_id", Auth::user()->user_id)->get();
        $data = [
                    "logs" => $logData,
                    "user" => Auth::user()->user_id,
                ];
        return view('history', $data);
    }
}
