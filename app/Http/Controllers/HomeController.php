<?php

namespace App\Http\Controllers;

use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $latestResult = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
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
