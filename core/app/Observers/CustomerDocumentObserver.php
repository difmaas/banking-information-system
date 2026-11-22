<?php

namespace App\Observers;

use App\Modules\Cis\Models\CustomerDocument;
use App\Services\AuditLogger;

class CustomerDocumentObserver
{
    public function created(CustomerDocument $document): void
    {
        $this->log('uploaded', $document);
    }

    public function deleted(CustomerDocument $document): void
    {
        $this->log('deleted', $document);
    }

    private function log(string $action, CustomerDocument $document): void
    {
        app(AuditLogger::class)->log(
            'customer_document.'.$action,
            CustomerDocument::class,
            $document->id,
            [
                'customer_id' => $document->customer_id,
                'type' => $document->type,
                'name' => $document->name,
            ],
        );
    }
}
