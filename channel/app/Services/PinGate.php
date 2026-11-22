<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PinGate
{
    public function setPin(User $user, string $pin): void
    {
        $user->forceFill([
            'pin_hash' => Hash::make($pin),
            'pin_set_at' => now(),
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ])->save();

        $this->clearVerification();
        $this->unlockSession();
    }

    public function verify(User $user, string $pin): bool
    {
        if ($user->isPinLocked()) {
            return false;
        }

        if (! Hash::check($pin, (string) $user->pin_hash)) {
            $this->recordFailure($user);

            return false;
        }

        $this->clearFailures($user);
        $this->markVerified();
        $this->unlockSession();

        return true;
    }

    public function isVerified(): bool
    {
        $verifiedAt = session('pin_verified_at');

        if (! is_numeric($verifiedAt)) {
            return false;
        }

        $verifiedTimestamp = (int) $verifiedAt;

        return now()->getTimestamp() - $verifiedTimestamp <= ((int) config('pin.verification_ttl_minutes', 10) * 60);
    }

    public function markVerified(): void
    {
        session(['pin_verified_at' => now()->timestamp]);
    }

    public function clearVerification(): void
    {
        session()->forget('pin_verified_at');
    }

    public function lockSession(): void
    {
        session(['pin_locked' => true]);
        $this->clearVerification();
    }

    public function unlockSession(): void
    {
        session()->forget('pin_locked');
    }

    public function isLocked(): bool
    {
        return (bool) session('pin_locked', false);
    }

    public function remainingLockSeconds(User $user): int
    {
        if ($user->pin_locked_until === null) {
            return 0;
        }

        return max(0, $user->pin_locked_until->getTimestamp() - now()->getTimestamp());
    }

    private function recordFailure(User $user): void
    {
        $attempts = $user->pin_failed_attempts + 1;
        $lockedUntil = $attempts >= (int) config('pin.max_attempts', 5)
            ? now()->addMinutes((int) config('pin.lock_minutes', 15))
            : null;

        $user->forceFill([
            'pin_failed_attempts' => $attempts,
            'pin_locked_until' => $lockedUntil,
        ])->save();
    }

    private function clearFailures(User $user): void
    {
        $user->forceFill([
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ])->save();
    }
}
