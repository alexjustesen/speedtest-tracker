<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetOoklaSpeedtestServers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OoklaController extends ApiController
{
    /**
     * GET /api/v1/ookla/list-servers
     * List available Ookla speedtest servers.
     */
    public function listServers(Request $request)
    {
        if ($request->user()->tokenCant('ookla:list-servers')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view speedtest servers.',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        $servers = GetOoklaSpeedtestServers::forApi();

        return $this->sendResponse(
            data: $servers,
            message: 'Speedtest servers fetched successfully.'
        );
    }
}
