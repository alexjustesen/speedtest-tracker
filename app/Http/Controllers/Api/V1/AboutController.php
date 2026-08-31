<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class AboutController extends ApiController
{
    /**
     * GET /api/v1/about
     * Return application information including name, build version and build date.
     */
    public function __invoke(Request $request)
    {
        return $this->sendResponse(
            data: [
                'name' => config('app.name'),
                'build_version' => config('speedtest.build_version'),
                'build_date' => config('speedtest.build_date')?->toDateString(),
            ]
        );
    }
}
