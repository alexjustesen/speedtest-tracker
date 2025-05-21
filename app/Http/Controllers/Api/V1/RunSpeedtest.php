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
        summary: 'Run a new Ookla speedtest',
        operationId: 'runSpeedtest',
        tags: ['Speedtests'],
        parameters: [
            new OA\Parameter(
                name: 'server_id',
                in: 'query',
                description: 'Optional Ookla speedtest server ID',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/QueuedResultResponse')
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
                    example: ['message' => 'You do not have permission to run speedtests.']
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
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
