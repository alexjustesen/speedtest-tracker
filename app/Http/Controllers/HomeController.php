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
        return view(
            Result::count()
                ? 'dashboard'
                : 'get-started'
        );
    }
}
