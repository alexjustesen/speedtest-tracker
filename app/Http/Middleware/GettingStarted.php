<?php

namespace App\Http\Middleware;

use App\Enums\ResultStatus;
use App\Models\Result;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GettingStarted
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Result::where('status', '=', ResultStatus::Completed)->doesntExist()) {
            return redirect()->route('getting-started');
        }

        return $next($request);
    }
}
