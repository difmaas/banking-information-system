<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class LoanScheduleController extends Controller
{
    public function installments(int $customer, int $loan, LnsService $lns): JsonResponse
    {
        try {
            return response()->json($lns->installments($customer, $loan));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function payments(int $customer, int $loan, LnsService $lns): JsonResponse
    {
        try {
            return response()->json($lns->payments($customer, $loan));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
