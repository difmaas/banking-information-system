<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;

class ParameterController extends Controller
{
    public function requiredDocuments(LnsService $lns): JsonResponse
    {
        return response()->json($lns->requiredDocuments());
    }
}
