<?php

namespace App\Http\Controllers;

use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function gettingStarted()
    {
        return view('getting-started');
    }

    public function home(Request $request)
    {
        $latestResult = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        return view('dashboard', [
            'latestResult' => $latestResult,
        ]);
    }
}
