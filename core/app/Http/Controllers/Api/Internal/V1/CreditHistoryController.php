<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Cis\Services\CisService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CreditHistoryController extends Controller
{
    public function index(int $customer, CisService $cis): JsonResponse
    {
        try {
            return response()->json($cis->creditHistory($customer));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
