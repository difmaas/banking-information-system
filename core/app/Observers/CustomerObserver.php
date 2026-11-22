<?php

namespace App\Observers;

use App\Modules\Cis\Models\Customer;
use App\Services\AuditLogger;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        $this->log('created', $customer);
    }

    public function updated(Customer $customer): void
    {
        $this->log('updated', $customer, [
            'changes' => array_keys($customer->getChanges()),
        ]);
    }

    public function deleted(Customer $customer): void
    {
        $this->log('deleted', $customer);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function log(string $action, Customer $customer, array $meta = []): void
    {
        app(AuditLogger::class)->log(
            'customer.'.$action,
            Customer::class,
            $customer->id,
            array_merge(['channel_user_id' => $customer->channel_user_id], $meta),
        );
    }
}
