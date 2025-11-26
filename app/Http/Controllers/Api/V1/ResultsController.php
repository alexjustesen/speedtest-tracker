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
            'page.size' => 'integer|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: 422
            );
        }

        $results = QueryBuilder::for(Result::class)
            ->allowedFilters([
                AllowedFilter::operator('ping', FilterOperator::DYNAMIC),
                AllowedFilter::operator('download', FilterOperator::DYNAMIC),
                AllowedFilter::operator('upload', FilterOperator::DYNAMIC),
                AllowedFilter::exact('healthy')->nullable(),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('scheduled'),
                AllowedFilter::operator(
                    name: 'start_at',
                    internalName: 'created_at',
                    filterOperator: FilterOperator::DYNAMIC,
                ),
                AllowedFilter::operator(
                    name: 'end_at',
                    internalName: 'created_at',
                    filterOperator: FilterOperator::DYNAMIC,
                ),
            ])
            ->allowedSorts([
                'ping',
                'download',
                'upload',
                'created_at',
                'updated_at',
            ])
            ->jsonPaginate(500, 25);;

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
     * Fetch the single most recent result.
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
        $result = Result::latest()
            ->firstOrFail();

        return $this->sendResponse(
            data: new ResultResource($result)
        );
    }
}