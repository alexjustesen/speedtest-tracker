<?php

namespace App\Helpers;

use App\Models\Speedtest;

class SpeedtestHelper
{

    /**
     * Returns the latest speedtest object.
     *
     * @return boolean|\App\Speedtest
     */
    public static function latest()
    {
        $data = Speedtest::latest()->get();

        if ($data->isEmpty()) {
            return false;
        }

        return $data->first();
    }

}
