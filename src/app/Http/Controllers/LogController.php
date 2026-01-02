<?php

namespace App\Http\Controllers;

use App\Models\Log;

class LogController extends Controller
{
    public function logs()
    {
        $logs = Log::orderBy('created_at', 'desc')->paginate(50);
        return response()->json($logs);
    }
}
