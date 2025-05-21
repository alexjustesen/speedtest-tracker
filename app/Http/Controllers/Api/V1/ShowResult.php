<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use App\OpenApi\Support\ResultExamples;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ShowResult extends ApiController
{
    #[OA\Get(
        path: '/api/v1/results/{id}',
        description: 'Get result.',
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
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    example: ResultExamples::SINGLE
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Result not found',
                content: new OA\JsonContent(
                    example: ResultExamples::NOT_FOUND
                )
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
