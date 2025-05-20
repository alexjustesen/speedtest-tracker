<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetOoklaSpeedtestServers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ListSpeedtestServers extends ApiController
{
    #[OA\Get(
        path: '/api/v1/speedtests/servers',
        description: 'Get a list of available Ookla speedtest servers.',
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'OK'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
        ]
    )]
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('speedtests:run')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to view speedtest servers.',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        $servers = GetOoklaSpeedtestServers::run();

        return self::sendResponse(
            data: $servers,
            message: 'Speedtest servers fetched successfully.'
        );
    }
}
