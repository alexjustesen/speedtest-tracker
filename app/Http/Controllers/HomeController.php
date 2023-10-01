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
        $hasResults = Result::count() > 0;

        return view($hasResults ? 'dashboard' : 'get-started');
    }
}
