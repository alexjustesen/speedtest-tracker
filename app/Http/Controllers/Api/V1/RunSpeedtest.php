<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ookla\StartSpeedtest;
use App\Http\Resources\V1\ResultResource;
use Illuminate\Http\Request;

class RunSpeedtest extends ApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('speedtests:run')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to run speedtests.',
                code: 403,
            );
        }

        $validated = $request->validate([
            'server_id' => ['sometimes', 'integer'],
        ]);

        $result = StartSpeedtest::run(
            serverId: $validated['server_id'] ?? null,
        );

        return self::sendResponse(
            data: new ResultResource($result),
            message: 'Speedtest added to the queue.',
        );
    }
}
