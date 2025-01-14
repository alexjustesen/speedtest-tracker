<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'Speedtest Tracker API', version: '1.0.0')]
abstract class ApiController
{
    /**
     * Send a response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendResponse($data, $message = 'ok', $code = 200)
    {
        $response = array_filter([
            'data' => $data,
            'message' => $message,
        ]);

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Throw an exception.
     *
     * @param  \Exception  $e
     * @param  int  $code
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function throw($e, $code = 500)
    {
        Log::info($e);

        throw new HttpResponseException(
            response: response()->json([
                'message' => $e->getMessage(),
            ], $code)
        );
    }
}
