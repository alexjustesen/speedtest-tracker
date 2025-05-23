<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema as OASchema;

class TokenController extends ApiController
{
    #[OA\Post(
        path: '/api/v1/app/tokens',
        summary: 'Issue a new API token',
        description: 'Generate a fresh API token with chosen scopes.',
        operationId: 'createToken',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['name', 'scopes'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'Label for the token',
                    ),
                    new OA\Property(
                        property: 'scopes',
                        type: 'array',
                        description: 'Permissions granted to this token',
                        items: new OA\Items(type: 'string')
                    ),
                    new OA\Property(
                        property: 'expires_at',
                        type: 'string',
                        format: 'date',
                        description: 'Optional expiration date (Y-m-d)',
                        example: '2025-08-18'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Token successfully created',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiTokenCreated')
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
        ]
    )]
    public function store(Request $request)
    {
        if ($request->user()->tokenCant('tokens:write')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to create API tokens.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('personal_access_tokens', 'name')
                    ->where('tokenable_id', $request->user()->id),
            ],
            'scopes' => 'required|array',
            'scopes.*' => 'in:results:read,speedtests:run,servers:list,tokens:write,tokens:read',
            'expires_at' => 'nullable|date',
        ]);

        $expires = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

        $token = $request->user()->createToken($data['name'], $data['scopes'], $expires);

        return self::sendResponse(
            data: [
                'token' => $token->plainTextToken,
                'scopes' => $data['scopes'],
                'expires_at' => $token->accessToken?->expires_at?->toIso8601String(),
            ],
            message: 'API token created.',
            code: Response::HTTP_CREATED
        );
    }

    #[Delete(
        path: '/api/v1/app/tokens/{id}',
        summary: 'Revoke an existing API token',
        description: 'Delete an API token by its ID.',
        operationId: 'revokeToken',
        tags: ['Authentication'],
        parameters: [
            new Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'The ID of the token to revoke',
                schema: new OASchema(type: 'integer')
            ),
        ],
        responses: [
            new OAResponse(
                response: Response::HTTP_OK,
                description: 'Token revoked successfully',
                content: new JsonContent(
                    example: ['message' => 'Token revoked.']
                )
            ),
            new OAResponse(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OAResponse(
                response: Response::HTTP_NOT_FOUND,
                description: 'Token not found',
                content: new JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->tokenCant('tokens:write')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to delete API tokens.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $token = $request->user()->tokens()->where('id', $id)->first();
        if (! $token) {
            return self::sendResponse(
                data: null,
                message: 'Token not found.',
                code: Response::HTTP_NOT_FOUND
            );
        }

        $token->delete();

        return self::sendResponse(
            data: null,
            message: 'Token revoked.',
            code: Response::HTTP_OK
        );
    }

    #[OA\Put(
        path: '/api/v1/app/tokens/{id}',
        summary: 'Edit an existing API token',
        description: 'Update the scopes and optional expiration date for an API token.',
        operationId: 'editToken',
        tags: ['Authentication'],
        parameters: [
            new Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID of the token to update',
                schema: new OASchema(type: 'integer')
            ),
        ],
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                type: 'object',
                required: ['scopes'],
                properties: [
                    new OA\Property(
                        property: 'scopes',
                        type: 'array',
                        description: 'New permissions for the token',
                        items: new Items(type: 'string'),
                    ),
                    new OA\Property(
                        property: 'expires_at',
                        type: 'string',
                        format: 'date',
                        nullable: true,
                        description: 'Optional new expiration date (Y-m-d)',
                    ),
                ]
            )
        ),
        responses: [
            new OAResponse(
                response: Response::HTTP_OK,
                description: 'Token scopes updated',
                content: new JsonContent(
                    ref: '#/components/schemas/ApiTokenEdit'
                )
            ),
            new OAResponse(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OAResponse(
                response: Response::HTTP_NOT_FOUND,
                description: 'Token not found',
                content: new JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OAResponse(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: new JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function update(Request $request, int $id)
    {
        if ($request->user()->tokenCant('tokens:write')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to modify API tokens.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $data = $request->validate([
            'scopes' => 'required|array',
            'scopes.*' => 'in:results:read,speedtests:run,ookla:list-servers,tokens:write,tokens:read',
            'expires_at' => 'nullable|date',
        ]);

        if (! empty($data['expires_at'])) {
            $data['expires_at'] = Carbon::parse($data['expires_at']);
        }

        $token = $request->user()->tokens()->where('id', $id)->first();
        if (! $token) {
            return self::sendResponse(
                data: null,
                message: 'Token not found.',
                code: Response::HTTP_NOT_FOUND
            );
        }

        $token->forceFill([
            'abilities' => $data['scopes'],
            'expires_at' => $data['expires_at'] ?? null,
        ])->save();

        return self::sendResponse(
            data: [
                'id' => $token->id,
                'name' => $token->name,
                'scopes' => $token->abilities,
                'expires_at' => $token->expires_at ? $token->expires_at->toIso8601String() : null,
            ],
            message: 'Token scopes updated.',
            code: Response::HTTP_OK
        );
    }

    #[OA\Get(
        path: '/api/v1/app/tokens',
        summary: 'List API tokens',
        description: 'Retrieve all API tokens.',
        operationId: 'listTokens',
        tags: ['Authentication'],
        responses: [
            new OAResponse(
                response: Response::HTTP_OK,
                description: 'A list of active API tokens',
                content: new JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            description: 'List of API tokens',
                            items: new Items(ref: '#/components/schemas/ApiTokenList')
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            description: 'Response status message',
                            example: 'ok'
                        ),
                    ]
                )
            ),
            new OAResponse(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
        ]
    )]
    public function index(Request $request)
    {
        if ($request->user()->tokenCant('tokens:read')) {
            return self::sendResponse(
                data: null,
                message: 'You do not have permission to view API tokens.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $tokens = $request->user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'scopes' => $token->abilities,
                'expires_at' => $token->expires_at
                    ? $token->expires_at->toIso8601String()
                    : null,
            ];
        });

        return self::sendResponse(data: $tokens);
    }
}
