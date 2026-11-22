<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogger;

class UserObserver
{
    public function created(User $user): void
    {
        $this->log('created', $user);
    }

    public function updated(User $user): void
    {
        $this->log('updated', $user);
    }

    public function deleted(User $user): void
    {
        $this->log('deleted', $user);
    }

    private function log(string $action, User $user): void
    {
        app(AuditLogger::class)->log(
            'user.'.$action,
            User::class,
            $user->id,
            ['email' => $user->email],
        );
    }
}
