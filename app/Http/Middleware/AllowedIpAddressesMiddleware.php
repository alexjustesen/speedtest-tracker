<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowedIpAddressesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (blank(config('speedtest.allowed_ips'))) {
            return $next($request);
        }

        $allowedIps = explode(',', config('speedtest.allowed_ips'));

        return in_array($request->ip(), $allowedIps)
            ? $next($request)
            : abort(403);
    }
}
