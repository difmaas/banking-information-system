<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CoreGateway;
use App\Http\Requests\Document\StoreDocumentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request, CoreGateway $core): JsonResponse
    {
        return $this->success($core->getCustomerDocuments($request->user()->id));
    }

    public function store(StoreDocumentRequest $request, CoreGateway $core): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('documents/'.$request->user()->id, 'local');

        return $this->success($core->storeCustomerDocument($request->user()->id, [
            'type' => $request->string('type')->toString(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]), 'Dokumen berhasil diunggah.', 201);
    }
}
