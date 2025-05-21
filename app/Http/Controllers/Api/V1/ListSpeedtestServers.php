<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetOoklaSpeedtestServers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ListSpeedtestServers extends ApiController
{
    #[OA\Get(
        path: '/api/v1/ookla/list-servers',
        summary: 'List available Ookla speedtest servers',
        description: 'Requires an API token with scope `ookla:list-servers`.',
        operationId: 'listSpeedtestServers',
        tags: ['Servers'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ServersCollection'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError',
                    example: ['message' => 'You do not have permission to view speedtest servers.']
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
