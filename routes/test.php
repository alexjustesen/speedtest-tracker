<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'test',
    ],
    function () {
        // silence is golden

        Route::get('/', function () {

            // https://github.com/striebwj/laravel-ping/blob/master/src/LaravelPing.php

            // create a new cURL resource
            $ch = curl_init('https://alexjustesen.com/');
            curl_setopt($ch, CURLOPT_USERAGENT, 'Speedtest Tracker (https://docs.speedtest-tracker.dev)');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // grab URL and pass it to the browser
            curl_exec($ch);

            $data = curl_getinfo($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);

            dd($data);
        });
    }
);
