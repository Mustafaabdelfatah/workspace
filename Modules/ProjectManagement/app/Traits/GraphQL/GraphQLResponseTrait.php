<?php

namespace Modules\ProjectManagement\App\Traits\GraphQL;

trait GraphQLResponseTrait
{
    /**
     * Build a successful GraphQL response
     */
    protected function successResponse(string $message, $record = null): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'record' => $record
        ];
    }

    /**
     * Build an error GraphQL response
     */
    protected function errorResponse(string $message, $record = null): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'record' => $record
        ];
    }

    /**
     * Build a successful list response with pagination
     */
    protected function successListResponse(string $message, $paginatedData): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $paginatedData->items(),
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
            ]
        ];
    }

    /**
     * Build an error list response
     */
    protected function errorListResponse(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'data' => [],
            'pagination' => null
        ];
    }

    /**
     * Build a simple success response (for operations like delete)
     */
    protected function simpleSuccessResponse(string $message): array
    {
        return [
            'status' => 'success',
            'message' => $message
        ];
    }

    /**
     * Build a simple error response (for operations like delete)
     */
    protected function simpleErrorResponse(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message
        ];
    }
}
