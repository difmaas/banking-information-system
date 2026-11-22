<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class LoanSimulationController extends Controller
{
    public function simulate(Request $request, LnsService $lns): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:1'],
            'tenor_months' => ['required', 'integer', 'min:1'],
        ]);

        try {
            return response()->json($lns->simulateInstallment(
                (int) $validated['product_id'],
                (float) $validated['amount'],
                (int) $validated['tenor_months'],
            ));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
