<?php

namespace App\Http\Controllers\API\Speedtest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Jobs\ExecSpeedtest;
use App\Models\JobTracking;
use App\Models\JobTrackingStatusEnum;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Str;

class MeasurementController extends Controller
{

    public function createNew(): JsonResponse
    {
        $uuid = Str::uuid()->toString();

        $config = [];
        $settings = new GeneralSettings();
        if (is_array($settings->speedtest_server) && count($settings->speedtest_server)) {
            $config = array_merge($config, [
                'ookla_server_id' => Arr::random($settings->speedtest_server),
            ]);
        }
        try {
            ExecSpeedtest::dispatch(
                speedtest: $config,
                tracking_key: $uuid,
                scheduled: false,
                tracked: true
            );
            $this->createTracking($uuid);
        } catch (\Throwable $th) {
            Log::warning($th);
            return response()->json(['exception' => $th]);

        }

        // Return the UUID to the API caller
        return response()->json(['uuid' => $uuid]);
    }

    public function getLatest(): JsonResponse
    {
        $latest = Result::query()
            ->latest()
            ->first();

        if (!$latest) {
            return response()->json([
                'message' => 'No results found.',
            ], 404);
        }

        return response()->json([
            'message' => 'ok',
            'data' => new ResultResource($latest),
        ]);
    }

    public function getMeasurementByTrackingId(string $uuid)
    {
        $trackingData = JobTracking::query()->where('tracking_key', $uuid)->first();
        if ($trackingData == null) {
            return response()->json([
                'status' => 'Not Found',
                'trackingid' => $uuid,
                'message' => "The requested speedtest tracking information, could not be found."], 404);
        }
        if ($trackingData->status != 'complete') {
            return response()->json([
                'status' => $trackingData->status,
                'trackingid' => $uuid,
                'message' => "The requested speedtest is currently {$trackingData->status}"]);
        }

        $result = Result::find($trackingData->result_id);
        return response(new ResultResource($result));

    }


    private function createTracking(string $tracking_key): void
    {
        JobTracking::create([
            'tracking_key' => $tracking_key,
            'status' => JobTrackingStatusEnum::Queued,
            'result_id' => null
        ]);
    }


}
