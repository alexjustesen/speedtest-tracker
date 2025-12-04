<?php

namespace App\Http\Integrations\Unifi;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class UnifiConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('services.unifi-api.base_url').'/proxy/network/integration/v1';
    }

    /**
     * Default config for the connector
     */
    protected function defaultConfig(): array
    {
        return [
            'verify' => false,
        ];
    }

    /**
     * Default headers for the connector
     */
    protected function defaultHeaders(): array
    {
        return [
            'X-API-KEY' => config('services.unifi-api.token'),
        ];
    }
}
