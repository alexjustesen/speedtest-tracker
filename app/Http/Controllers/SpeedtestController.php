<?php

namespace App\Http\Controllers;

use App\Helpers\SpeedtestHelper;
use App\Models\Result;
use App\Models\Speedtest;
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
        $data = SpeedtestController::getData();

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
     * Return latest speedtest
     *
     * @return mixed
     */
    private function getData()
    {
        $data = SpeedtestController::getLatest();

        // Homepage expects this to in Mbps.  This calculation matches the results shown in the UI.
        if ($data['download']) {
            $data['download'] /= 125000;
        }
        if ($data['upload']) {
            $data['upload'] /= 125000;
        }

        $response = [
            'data' => $data,
        ];

        return $response;
    }
    
    /**
     * Returns the latest speedtest object.
     *
     * @return boolean|\App\Speedtest
     */
    public static function getLatest()
    {
        $data = Result::latest()->get();

        if ($data->isEmpty()) {
            return false;
        }

        return $data->first();
    }

}
