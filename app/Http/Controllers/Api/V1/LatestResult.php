<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class LatestResult extends ApiController
{
    #[OA\Get(
        path: '/api/v1/results/latest',
        summary: 'Fetch the single most recent result',
        description: 'Requires an API token with scope `results:read`.',
        operationId: 'getLatestResult',
        tags: ['Results'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/Result')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'No result found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function __invoke(Request $request)
    {
        $result = Result::query()
            ->latest()
            ->firstOr(function () {
                self::throw(
                    e: new NotFoundException('No result found.'),
                    code: 404,
                );
            });

        return self::sendResponse(
            data: new ResultResource($result),
        );
    }
}
