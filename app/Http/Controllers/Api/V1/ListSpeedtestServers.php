<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetOoklaSpeedtestServers;
use App\OpenApi\Support\OoklaExamples;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ListSpeedtestServers extends ApiController
{
    #[OA\Get(
        path: '/api/v1/ookla/list-servers',
        description: 'Get a list of available Ookla speedtest servers.',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    example: OoklaExamples::LIST
                )
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    example: OoklaExamples::FORBIDDEN
                )
            ),
        ]
    )]
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('ookla:list-servers')) {
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
