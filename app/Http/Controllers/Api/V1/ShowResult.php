<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ShowResult extends ApiController
{
    #[OA\Get(
        path: '/api/v1/results/{id}',
        summary: 'Fetch a single result by ID',
        operationId: 'getResult',
        tags: ['Results'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'The ID of the result'
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/ResultResponse')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Result not found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function __invoke(Request $request, int $id)
    {
        $result = Result::findOr($id, function () {
            self::throw(
                e: new NotFoundException('Result not found.'),
                code: 404,
            );
        });

        return self::sendResponse(
            data: new ResultResource($result),
        );
    }
}
