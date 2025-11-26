<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardV2Controller extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (! config('speedtest.dashboard_v2.enabled')) {
            return redirect()->route('home');
        }

        return view('dashboard-v2');
    }
}
