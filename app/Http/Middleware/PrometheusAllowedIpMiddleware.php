<?php

namespace App\Http\Middleware;

use App\Helpers\Network;
use App\Settings\DataIntegrationSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusAllowedIpMiddleware
{
    public function __construct(
        protected DataIntegrationSettings $settings
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (blank($this->settings->prometheus_allowed_ips)) {
            return $next($request);
        }

        $clientIp = $request->ip();
        $allowedIps = $this->settings->prometheus_allowed_ips;

        foreach ($allowedIps as $allowedIp) {
            if (str_contains($allowedIp, '/')) {
                if (Network::ipInRange($clientIp, $allowedIp)) {
                    return $next($request);
                }
            } elseif ($clientIp === $allowedIp) {
                return $next($request);
            }
        }

        abort(403);
    }
}
