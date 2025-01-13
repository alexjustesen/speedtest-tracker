<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ResultResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class ListResults extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return ApiController::sendResponse(
                data: $validator->errors(),
                message: 'Validation failed.',
                code: 422,
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
                AllowedFilter::operator('created_at', FilterOperator::DYNAMIC),
                AllowedFilter::operator('updated_at', FilterOperator::DYNAMIC),
            ])
            ->allowedSorts([
                'ping',
                'download',
                'upload',
                'created_at',
                'updated_at',
            ])
            ->jsonPaginate($request->input('per_page', 25));

        return ResultResource::collection($results);
    }
}
