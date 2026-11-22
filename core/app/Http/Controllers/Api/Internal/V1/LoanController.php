<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class LoanController extends Controller
{
    public function index(int $customer, LnsService $lns): JsonResponse
    {
        try {
            return response()->json($lns->loans($customer));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function store(Request $request, int $customer, LnsService $lns): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:1'],
            'tenor_months' => ['required', 'integer', 'min:1'],
        ]);

        try {
            return response()->json($lns->createLoan($customer, $validated), 201);
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function show(int $customer, int $loan, LnsService $lns): JsonResponse
    {
        try {
            return response()->json($lns->loan($customer, $loan));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
