<?php

namespace App\Livewire;

use App\Services\PinGate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PinSetup extends Component
{
    public string $current_password = '';

    public string $pin = '';

    public string $pin_confirmation = '';

    public function save(PinGate $gate): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'pin' => [
                'required',
                'digits:6',
                'confirmed',
                function (string $attribute, mixed $value, callable $fail): void {
                    if (in_array((string) $value, config('pin.weak_pins', []), true)) {
                        $fail('PIN terlalu sederhana. Gunakan kombinasi lain.');
                    }
                },
            ],
        ]);

        $gate->setPin(Auth::user(), $this->pin);
        session()->regenerate();

        $this->dispatch('toast', type: 'success', description: 'PIN berhasil dibuat.');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pin-setup')->layout('layouts.auth');
    }
}
