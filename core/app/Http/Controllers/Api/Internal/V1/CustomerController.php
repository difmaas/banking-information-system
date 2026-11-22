<?php

namespace App\Http\Controllers\Api\Internal\V1;

use App\Modules\Cis\Services\CisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class CustomerController extends Controller
{
    public function store(Request $request, CisService $cis): JsonResponse
    {
        $validated = $request->validate([
            'channel_user_id' => ['required', 'integer', 'min:1'],
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('customers', 'nik')->ignore((int) $request->input('channel_user_id'), 'channel_user_id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            return response()->json($cis->createCustomer((int) $validated['channel_user_id'], $validated), 201);
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function show(int $customer, CisService $cis): JsonResponse
    {
        try {
            return response()->json($cis->profile($customer));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }

    public function update(Request $request, int $customer, CisService $cis): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['sometimes', 'string', 'max:30'],
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'monthly_income' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        try {
            return response()->json($cis->updateProfile($customer, $validated));
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception);
        }
    }
}
