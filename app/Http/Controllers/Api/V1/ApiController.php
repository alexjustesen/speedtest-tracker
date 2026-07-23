<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;

abstract class ApiController
{
    /**
     * Date-range filters aliasing a timestamp column.
     *
     * start_at -> column >= value (start of day for date-only input)
     * end_at   -> column <= value (end of day for date-only input)
     *
     * @return array<int, AllowedFilter>
     */
    protected function dateRangeFilters(string $column = 'created_at'): array
    {
        return [
            AllowedFilter::operator(
                name: 'start_at',
                filterOperator: FilterOperator::GREATER_THAN_OR_EQUAL,
                internalName: $column,
            ),
            AllowedFilter::callback('end_at', function (Builder $query, $value) use ($column) {
                $date = Carbon::parse($value);

                // Date-only input (no time component) should include the whole day.
                if (is_string($value) && ! str_contains($value, ':')) {
                    $date->endOfDay();
                }

                $query->where($query->qualifyColumn($column), '<=', $date);
            }),
        ];
    }

    /**
     * Send a response.
     *
     * @param  mixed  $data
     * @param  array  $filters
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    public static function sendResponse($data, $filters = [], $message = 'ok', $code = 200)
    {
        $response = array_filter([
            'data' => $data,
            'filters' => $filters,
            'message' => $message,
        ]);

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json(
            data: $response,
            status: $code,
        );
    }

    /**
     * Throw an exception.
     *
     * @param  Exception  $e
     * @param  int  $code
     *
     * @throws HttpResponseException
     */
    public static function throw($e, $code = 500)
    {
        Log::info($e);

        $response = [
            'message' => $e->getMessage(),
        ];

        throw new HttpResponseException(
            response: response()->json(
                data: $response,
                status: $code,
            ),
        );
    }
}
