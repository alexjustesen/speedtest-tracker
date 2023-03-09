<?php

namespace App\Actions;

use App\Helpers\SettingsHelper;
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

        if (SettingsHelper::get('show_average')) {
            $avg = Speedtest::select(DB::raw('AVG(ping) as ping, AVG(download) as download, AVG(upload) as upload'))
                ->where('failed', false)
                ->first()
                ->toArray();
            $response['average'] = $avg;
        }

        if (SettingsHelper::get('show_max')) {
            $max = Speedtest::select(DB::raw('MAX(ping) as ping, MAX(download) as download, MAX(upload) as upload'))
                ->where('failed', false)
                ->first()
                ->toArray();
            $response['maximum'] = $max;
        }

        if (SettingsHelper::get('show_min')) {
            $min = Speedtest::select(DB::raw('MIN(ping) as ping, MIN(download) as download, MIN(upload) as upload'))
                ->where('failed', false)
                ->first()
                ->toArray();
            $response['minimum'] = $min;
        }

        return $response;
    }
}
