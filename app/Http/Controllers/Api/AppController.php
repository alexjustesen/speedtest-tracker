<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class AppController
{
    public function healthcheck(): JsonResponse
    {
        return response()->json([
            'message' => 'Speedtest Tracker is running!',
        ]);
    }
}
