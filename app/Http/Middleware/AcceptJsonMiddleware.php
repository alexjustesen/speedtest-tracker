<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AcceptJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Check if the Accept header includes application/json
        if (! $request->acceptsJson()) {
            return response()->json([
                'message' => 'This endpoint only accepts JSON. Please include "Accept: application/json" in your request headers.',
                'error' => 'Unsupported Media Type',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        // Ensure the response is JSON
        $response = $next($request);

        // Force JSON content type if not already set
        if (! $response->headers->has('Content-Type') ||
            ! str_contains($response->headers->get('Content-Type'), 'application/json')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
