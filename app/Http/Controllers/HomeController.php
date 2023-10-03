<?php

namespace App\Http\Controllers;

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
