<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    public function created(Role $role): void
    {
        $this->log('created', $role);
    }

    public function updated(Role $role): void
    {
        $this->log('updated', $role, ['changes' => array_keys($role->getChanges())]);
    }

    public function deleted(Role $role): void
    {
        $this->log('deleted', $role);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function log(string $action, Role $role, array $meta = []): void
    {
        app(AuditLogger::class)->log(
            'role.'.$action,
            Role::class,
            $role->id,
            array_merge(['name' => $role->name], $meta),
        );
    }
}
