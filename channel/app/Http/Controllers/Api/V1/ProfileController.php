<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, CoreGateway $core): JsonResponse
    {
        return $this->success($core->getCustomerProfile($request->user()->id));
    }

    public function update(UpdateProfileRequest $request, CoreGateway $core): JsonResponse
    {
        return $this->success(
            $core->updateCustomerProfile($request->user()->id, $request->validated()),
            'Profil nasabah diperbarui.',
        );
    }
}
