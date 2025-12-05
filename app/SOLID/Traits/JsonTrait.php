<?php

namespace App\SOLID\Traits;

trait JsonTrait
{
    public function whenDone($data,$message = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message == null ? 'Success' : $message,
            'data' => $data,
        ],200);
    }

    public function whenError($data)
    {
        return response()->json([
            'status' => false,
            'message' => $data,
            'data' => null,
        ],400);
    }

    public function api_error($data)
    {
        return ['status' => 'error', 'message' => $data];
    }

    public function api_done($data = null)
    {
        return ['status' => 'success', 'message' => $data];
    }
}
