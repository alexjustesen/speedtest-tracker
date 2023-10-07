<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $settings = new GeneralSettings();
        if (!$settings->public_dashboard_enabled) {
            return redirect()->route('filament.admin.auth.login');
        }

        $latestResult = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'successful', 'created_at'])
            ->latest()
            ->first();

        if (! $latestResult) {
            return view('get-started');
        }

        return view('dashboard', [
            'latestResult' => $latestResult,
        ]);
    }
}
