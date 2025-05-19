<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ookla\RunSpeedtest as RunSpeedtestAction;
use App\Http\Resources\V1\ResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class RunSpeedtest extends ApiController
{
    #[OA\Post(
        path: '/api/v1/speedtests/run',
        description: 'Run a new Ookla speedtest. Optionally provide a server_id.',
        parameters: [
            new OA\Parameter(
                name: 'server_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                description: 'Optional Ookla speedtest server ID'
            ),
        ],
        responses: [
            new OA\Response(response: Response::HTTP_CREATED, description: 'Created'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('speedtests:run')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to run speedtests.',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        $validator = Validator::make($request->all(), [
            'server_id' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return ApiController::sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = RunSpeedtestAction::run(
            serverId: $request->input('server_id'),
        );

        return self::sendResponse(
            data: new ResultResource($result),
            message: 'Speedtest added to the queue.',
            code: Response::HTTP_CREATED,
        );
    }
}
