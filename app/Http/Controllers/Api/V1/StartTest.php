<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ookla\StartSpeedtest;
use App\Enums\ResultStatus;
use App\Http\Resources\V1\ResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class StartTest extends ApiController
{
    #[OA\Post(
        path: '/api/v1/speedtests',
        description: 'Start a new Ookla speedtest. Optionally provide a server_id. Use ?await=true to wait for result.',
        parameters: [
            new OA\Parameter(
                name: 'server_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                description: 'Optional server ID'
            ),
            new OA\Parameter(
                name: 'await',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                description: 'If true, wait up to 60s for the test to complete.'
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Speedtest complete'),
            new OA\Response(response: 202, description: 'Speedtest started'),
        ]
    )]
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'nullable|integer',
        ]);

        $await = $request->boolean('await', false);

        $result = StartSpeedtest::run(
            scheduled: false,
            serverId: $validated['server_id'] ?? null
        );

        if (! $await) {
            return self::sendResponse(
                ['id' => $result->id],
                [],
                'Speedtest started.',
                Response::HTTP_ACCEPTED
            );
        }

        // Wait up to 60 seconds for the result to finish
        $timeout = 60;
        $start = now();

        while (
            ! in_array($result->fresh()->status, [
                ResultStatus::Completed,
                ResultStatus::Failed,
                ResultStatus::Skipped,
            ]) &&
            now()->diffInSeconds($start) < $timeout
        ) {
            usleep(500_000);
        }

        $refreshed = $result->fresh();

        $isFinished = in_array($refreshed->status, [
            ResultStatus::Completed,
            ResultStatus::Failed,
            ResultStatus::Skipped,
        ]);

        return self::sendResponse(
            new ResultResource($refreshed),
            [],
            $isFinished ? 'Speedtest finished.' : 'Speedtest time out, please check the Results table in the UI.',
            $isFinished ? Response::HTTP_OK : Response::HTTP_ACCEPTED
        );
    }
}
