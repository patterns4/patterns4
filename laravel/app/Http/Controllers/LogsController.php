<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogsController extends Controller
{
    public function show()
    {
        $logs = new Log();
        $data = ["logs" => $logs->all()];
        return view('logs', $data);
    }
}
