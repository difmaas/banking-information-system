<?php

namespace App\Console\Commands;

use App\Actions\EnsureCoreCustomer;
use App\Exceptions\CoreGatewayException;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('bis:provision-customers')]
#[Description('Provision core customer records for existing channel users.')]
class ProvisionCustomersCommand extends Command
{
    public function handle(EnsureCoreCustomer $provisioner): int
    {
        $total = 0;
        $failed = 0;

        User::query()->chunkById(100, function ($users) use ($provisioner, &$total, &$failed): void {
            foreach ($users as $user) {
                $total++;

                if (blank($user->nik)) {
                    $failed++;
                    $this->warn("User {$user->id}: NIK belum lengkap, dilewati.");

                    continue;
                }

                try {
                    $provisioner->handle($user);
                } catch (CoreGatewayException $exception) {
                    $failed++;
                    $this->warn("User {$user->id}: {$exception->getMessage()}");
                }
            }
        });

        $this->info("Processed {$total} users, {$failed} failed.");

        return self::SUCCESS;
    }
}
