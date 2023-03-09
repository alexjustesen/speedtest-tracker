<?php

namespace App\Helpers;

use App\Models\Result;

class SpeedtestHelper
{

    /**
     * Returns the latest speedtest object.
     *
     * @return boolean|\App\Speedtest
     */
    public static function latest()
    {
        $data = Result::latest()->get();

        if ($data->isEmpty()) {
            return false;
        }

        return $data->first();
    }

}
