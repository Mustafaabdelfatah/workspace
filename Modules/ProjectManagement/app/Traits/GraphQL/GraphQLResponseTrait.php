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
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'project' => $record
        ];
    }

    /**
     * Build an error GraphQL response
     */
    protected function errorResponse(string $message, $record = null): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'project' => $record
        ];
    }

    /**
     * Build a successful list response with pagination
     */
    protected function successListResponse(string $message, $paginatedData): array
    {
        return [
            'success' => true,
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
            'success' => false,
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
            'success' => true,
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
            'success' => false,
            'status' => 'error',
            'message' => $message
        ];
    }
}
