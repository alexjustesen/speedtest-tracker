<?php

namespace App\OpenApi\Annotations\V1;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\PathItem(
    path: '/api/v1/results',
    description: 'Endpoints for retrieving speedtest results.'
)]
class ResultsAnnotations
{
    #[OA\Get(
        path: '/api/v1/results',
        summary: 'List all results',
        operationId: 'listResults',
        tags: ['Results'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
            new OA\Parameter(
                name: 'per.page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 500, default: 25),
                description: 'Number of results per page'
            ),
            new OA\Parameter(
                name: 'filter[ping]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'number'),
                description: 'Filter by ping value (supports operators like >=, <=, etc.)'
            ),
            new OA\Parameter(
                name: 'filter[download]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                description: 'Filter by download speed (supports operators like >=, <=, etc.)'
            ),
            new OA\Parameter(
                name: 'filter[upload]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                description: 'Filter by upload speed (supports operators like >=, <=, etc.)'
            ),
            new OA\Parameter(
                name: 'filter[healthy]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                description: 'Filter by healthy status'
            ),
            new OA\Parameter(
                name: 'filter[status]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                description: 'Filter by status'
            ),
            new OA\Parameter(
                name: 'filter[scheduled]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                description: 'Filter by scheduled status'
            ),
            new OA\Parameter(
                name: 'filter[start_at]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                description: 'Filter results created on or after this date (alias for created_at>=)'
            ),
            new OA\Parameter(
                name: 'filter[end_at]',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                description: 'Filter results created on or before this date (alias for created_at<=)'
            ),
            new OA\Parameter(
                name: 'sort',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['ping', '-ping', 'download', '-download', 'upload', '-upload', 'created_at', '-created_at', 'updated_at', '-updated_at']),
                description: 'Sort results by field (prefix with - for descending)'
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/ResultsCollection')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function index(): void {}

    #[OA\Get(
        path: '/api/v1/results/{id}',
        summary: 'Get a single result',
        operationId: 'getResult',
        tags: ['Results'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'The ID of the result'
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/ResultResponse')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Result not found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function show(): void {}

    #[OA\Get(
        path: '/api/v1/results/latest',
        summary: 'Get the most recent result',
        operationId: 'getLatestResult',
        tags: ['Results'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/AcceptHeader'),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/Result')
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_ACCEPTABLE,
                description: 'Not Acceptable - Missing or invalid Accept header',
                content: new OA\JsonContent(ref: '#/components/schemas/NotAcceptableError')
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'No result found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function latest(): void {}
}