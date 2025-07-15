<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('speedtest.public_dashboard')) {
            return redirect()->route('filament.admin.auth.login');
        }

        return $next($request);
    }
}
