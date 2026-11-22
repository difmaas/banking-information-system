<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Cis\Services\CisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class DocumentController extends Controller
{
    public function index(int $customer, CisService $cis): JsonResponse
    {
        try {
            return response()->json($cis->documents($customer));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function store(Request $request, int $customer, CisService $cis): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:500'],
            'size' => ['nullable', 'integer', 'min:0'],
            'mime' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            return response()->json($cis->storeDocument($customer, $validated), 201);
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
