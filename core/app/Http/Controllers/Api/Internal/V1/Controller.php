<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use RuntimeException;

abstract class Controller extends BaseController
{
    protected function errorResponse(RuntimeException $exception): JsonResponse
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => $exception->getMessage(),
        ], 422);
    }
}
