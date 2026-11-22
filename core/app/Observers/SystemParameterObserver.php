<?php

namespace App\Observers;

use App\Models\SystemParameter;
use App\Services\AuditLogger;

class SystemParameterObserver
{
    public function created(SystemParameter $parameter): void
    {
        $this->log('created', $parameter);
    }

    public function updated(SystemParameter $parameter): void
    {
        $this->log('updated', $parameter, [
            'key' => $parameter->key,
            'changes' => array_keys($parameter->getChanges()),
        ]);
    }

    public function deleted(SystemParameter $parameter): void
    {
        $this->log('deleted', $parameter);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function log(string $action, SystemParameter $parameter, array $meta = []): void
    {
        app(AuditLogger::class)->log(
            'system_parameter.'.$action,
            SystemParameter::class,
            $parameter->id,
            array_merge(['key' => $parameter->key], $meta),
        );
    }
}
