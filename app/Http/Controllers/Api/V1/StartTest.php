<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ookla\StartSpeedtest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class StartTest extends ApiController
{
    #[OA\Post(
        path: '/api/v1/test/start',
        description: 'Start a new Ookla speedtest. Optionally provide a server_id.',
        parameters: [
            new OA\Parameter(
                name: 'server_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                description: 'Optional server ID'
            ),
        ],
        responses: [
            new OA\Response(response: 202, description: 'Speedtest started'),
        ]
    )]
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'nullable|integer',
        ]);

        $result = StartSpeedtest::run(
            scheduled: false,
            serverId: $validated['server_id'] ?? null
        );

        return self::sendResponse(
            ['id' => $result->id],
            [],
            'Speedtest started.',
            Response::HTTP_ACCEPTED
        );
    }
}
