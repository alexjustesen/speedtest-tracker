<?php

namespace App\Http\Integrations\Unifi\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListWanInterfacesRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $siteId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/sites/'.$this->siteId.'/wans';
    }
}
