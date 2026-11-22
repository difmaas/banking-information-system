<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditHistoryController extends Controller
{
    public function index(Request $request, CoreGateway $core): JsonResponse
    {
        return $this->success($core->getCustomerCreditHistory($request->user()->id));
    }
}
