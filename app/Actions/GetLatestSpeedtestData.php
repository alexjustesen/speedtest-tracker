<?php

namespace App\Actions;

use App\Helpers\SpeedtestHelper;
use App\Models\Speedtest;
use DB;
use Henrywhitaker3\LaravelActions\Interfaces\ActionInterface;

class GetLatestSpeedtestData implements ActionInterface
{
    /**
     * Run the action.
     *
     * @return mixed
     */
    public function run()
    {
        $data = SpeedtestHelper::latest();
        
        // Homepage expects this to in Mbps.  This calculation matches the results shown in the UI.
        $data['download'] /= 125000;
        $data['upload'] /= 125000;

        $response = [
            'data' => $data,
        ];

        return $response;
    }
}
