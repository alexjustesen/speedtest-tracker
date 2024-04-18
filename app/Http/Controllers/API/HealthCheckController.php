<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HealthCheckController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return response()->json([
            'message' => 'Speedtest Tracker is running!',
        ]);
    }
}
