<?php

namespace App\Http\Controllers;

use App\Actions\GetLatestSpeedtestData;
use App\Actions\QueueSpeedtest;
use App\Jobs\SpeedtestJob;
use App\Models\Speedtest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SpeedtestController extends Controller
{
  
    /**
     * Return latest speedtest
     *
     * @return  JsonResponse
     */
    public function latest()
    {
        $data = run(GetLatestSpeedtestData::class);
        
        if ($data['data']) {
            return response()->json($data, 200);
        } else {
            return response()->json([
                'method' => 'get latest speedtest',
                'error' => 'no speedtests have been run'
            ], 404);
        }
    }

    /**
     * Queue a new speedtest
     *
     * @return JsonResponse
     */
    public function run()
    {
        try {
            run(QueueSpeedtest::class);

            return response()->json([
                'method' => 'run speedtest',
                'data' => 'a new speedtest has been added to the queue'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'method' => 'run speedtest',
                'error' => $e
            ], 500);
        }
    }

}
