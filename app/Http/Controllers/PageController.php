<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function results(Request $request)
    {
        // TODO: authorize viewing this page

        return view('results');
    }
}
