<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Results;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class ListResults extends ApiController
{
    #[OA\Get(
        path: '/api/v1/results',
        summary: 'List results',
        operationId: 'listResults',
        tags: ['Results'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ResultsCollection'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
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
            ->jsonPaginate($request->input('per_page', 25));

        return Results::collection($results);
    }
}
