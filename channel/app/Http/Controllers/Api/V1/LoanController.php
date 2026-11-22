<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use App\Http\Requests\Loan\StoreLoanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request, CoreGateway $core): JsonResponse
    {
        return $this->success($core->getCustomerLoans($request->user()->id));
    }

    public function store(StoreLoanRequest $request, CoreGateway $core): JsonResponse
    {
        return $this->success($core->storeCustomerLoan($request->user()->id, $request->validated()), 'Pengajuan kredit berhasil dibuat.', 201);
    }

    public function show(Request $request, CoreGateway $core, int $loan): JsonResponse
    {
        return $this->success($core->getCustomerLoan($request->user()->id, $loan));
    }
}
