<?php

namespace App\Livewire;

use App\Services\PinGate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PinLock extends Component
{
    public string $pin = '';

    public bool $locked = false;

    public int $remaining = 0;

    public function mount(PinGate $gate): void
    {
        $this->locked = $gate->isLocked();
        $this->syncRemaining();
    }

    public function lockSession(PinGate $gate): void
    {
        $gate->lockSession();
        $this->locked = true;
        $this->syncRemaining();
    }

    public function unlock(PinGate $gate): void
    {
        $this->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        if ($user->isPinLocked()) {
            $this->addError('pin', 'Terlalu banyak percobaan. Coba lagi nanti.');
            $this->syncRemaining();

            return;
        }

        if (! $gate->verify($user, $this->pin)) {
            $this->addError('pin', 'PIN tidak valid.');
            $this->syncRemaining();

            return;
        }

        session()->regenerate();
        $this->locked = false;
        $this->remaining = 0;
        $this->reset('pin');

        $this->dispatch('toast', type: 'success', description: 'Sesi dibuka kembali.');
    }

    public function render(): View
    {
        return view('livewire.pin-lock');
    }

    private function syncRemaining(): void
    {
        $user = Auth::user();

        $this->remaining = $user === null
            ? 0
            : app(PinGate::class)->remainingLockSeconds($user);
    }
}
