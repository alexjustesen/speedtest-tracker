<?php

namespace App\Http\Controllers;

use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function gettingStarted()
    {
        if (Result::where('status', '=', ResultStatus::Completed)->count()) {
            return redirect()->route('home');
        }

        return view('getting-started');
    }

    public function home(Request $request)
    {
        $latestResult = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->first();

        if (! $latestResult) {
            return redirect()->route('getting-started');
        }

        return view('dashboard', [
            'latestResult' => $latestResult,
        ]);
    }
}
