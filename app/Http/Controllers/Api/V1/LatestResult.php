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
        description: 'Get the latest result.',
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'No result found'),
        ])]
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
