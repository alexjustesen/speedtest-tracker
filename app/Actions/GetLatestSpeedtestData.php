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

        $response = [
            'data' => $data,
        ];

        return $response;
    }
}
