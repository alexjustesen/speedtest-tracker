<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ookla\RunSpeedtest as RunSpeedtestAction;
use App\Http\Resources\V1\ResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SpeedtestController extends ApiController
{
    /**
     * POST /api/v1/speedtests/run
     * Run a new Ookla speedtest.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('speedtests:run')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to run speedtests.',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        $validator = Validator::make($request->all(), [
            'server_id' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = RunSpeedtestAction::run(
            scheduled: true,
            serverId: $request->input('server_id'),
            dispatchedBy: $request->user()->id,
        );

        return $this->sendResponse(
            data: new ResultResource($result),
            message: 'Speedtest added to the queue.',
            code: Response::HTTP_CREATED,
        );
    }
}
