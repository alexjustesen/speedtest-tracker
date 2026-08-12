<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class ResultsController extends ApiController
{
    /**
     * GET /results
     * List or filter results with optional pagination.
     */
    public function list(Request $request)
    {
        if ($request->user()->tokenCant('results:read')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view results.',
                code: Response::HTTP_FORBIDDEN
            );
        }
        $validator = Validator::make($request->all(), [
            'page.size' => 'integer|min:1|max:'.config('json-api-paginate.max_results'),
            ...$this->dateFilterValidationRules(),
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: 422
            );
        }

        $results = QueryBuilder::for(Result::class)
            ->allowedFilters($this->allowedResultFilters())
            ->allowedSorts([
                'ping',
                'download',
                'upload',
                'created_at',
                'updated_at',
            ])
            ->jsonPaginate();

        return ResultResource::collection($results);
    }

    /**
     * GET /results/{id}
     * Fetch a single result by ID.
     */
    public function show(Request $request, int $id)
    {
        if ($request->user()->tokenCant('results:read')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view results.',
                code: Response::HTTP_FORBIDDEN
            );
        }
        $result = Result::findOr($id, function () {
            self::throw(
                e: new NotFoundException('Result not found.'),
                code: 404
            );
        });

        return $this->sendResponse(
            data: new ResultResource($result)
        );
    }

    /**
     * GET /results/latest
     * Fetch the single most recent result, optionally filtered (e.g. filter[status]=completed
     * for the last known good result).
     */
    public function latest(Request $request)
    {
        if ($request->user()->tokenCant('results:read')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view results.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $validator = Validator::make($request->all(), $this->dateFilterValidationRules());

        if ($validator->fails()) {
            return $this->sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: 422
            );
        }

        $result = QueryBuilder::for(Result::class)
            ->allowedFilters($this->allowedResultFilters())
            ->latest()
            ->firstOrFail();

        return $this->sendResponse(
            data: new ResultResource($result)
        );
    }

    /**
     * Validation rules for the date-range filters shared by the list and latest endpoints.
     *
     * @return array<string, string>
     */
    private function dateFilterValidationRules(): array
    {
        return [
            'filter.start_at' => 'sometimes|date',
            'filter.end_at' => 'sometimes|date',
        ];
    }

    /**
     * Filters shared by the list and latest endpoints.
     *
     * @return array<int, AllowedFilter>
     */
    private function allowedResultFilters(): array
    {
        return [
            AllowedFilter::operator('ping', FilterOperator::DYNAMIC),
            AllowedFilter::operator('download', FilterOperator::DYNAMIC),
            AllowedFilter::operator('upload', FilterOperator::DYNAMIC),
            AllowedFilter::exact('healthy')->nullable(),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('scheduled'),
            ...$this->dateRangeFilters(),
        ];
    }
}
