<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Cis\Services\CisService;
use App\Modules\Gls\Services\GlsService;
use App\Modules\Lns\Services\LnsService;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function index(CisService $cis, LnsService $lns, GlsService $gls): JsonResponse
    {
        return response()->json([
            'data' => [
                'cis' => $cis->summary(),
                'lns' => $lns->summary(),
                'gls' => $gls->summary(),
            ],
        ]);
    }
}