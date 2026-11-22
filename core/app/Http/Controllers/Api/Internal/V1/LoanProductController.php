<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;

class LoanProductController extends Controller
{
    public function index(LnsService $lns): JsonResponse
    {
        return response()->json($lns->products());
    }
}
