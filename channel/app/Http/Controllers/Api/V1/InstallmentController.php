<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index(Request $request, CoreGateway $core, int $loan): JsonResponse
    {
        return $this->success($core->getCustomerLoanInstallments($request->user()->id, $loan));
    }
}
