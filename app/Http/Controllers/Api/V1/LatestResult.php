<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;

class LatestResult extends ApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $result = Result::query()
            ->latest()
            ->firstOr(function () {
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
