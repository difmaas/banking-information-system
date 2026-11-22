<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(CoreGateway $core): JsonResponse
    {
        return $this->success($core->getLoanProducts());
    }
}
