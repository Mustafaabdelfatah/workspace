<?php

namespace App\SOLID\Services;

class ApiResponseGeneratorList
{
    public static function generateSuccessResponse($data)
    {
        return [
            'status' => true,
            'message' => 'Fetch Successfully',
            'total' => $data->total(),
            'count' => $data->count(),
            'perPage' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $data->items(),
        ];
    }
}
