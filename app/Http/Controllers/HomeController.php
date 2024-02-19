<?php

namespace App\Http\Controllers;

use App\Enums\ResultStatus;
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

        if (! $settings->public_dashboard_enabled) {
            return redirect()->route('filament.admin.auth.login');
        }

        $latestResult = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        if (! $latestResult) {
            return view('get-started');
        }

        /**
         * This jank needs to happen because some people like
         * to watch the world burn by setting a time zone
         * in their database instances.
         */
        if ($settings->db_has_timezone) {
            date_default_timezone_set($settings->timezone ?? 'UTC');
        }

        $diff = $latestResult->created_at->diffForHumans();

        return view('dashboard', [
            'diff' => $diff,
            'latestResult' => $latestResult,
        ]);
    }
}
