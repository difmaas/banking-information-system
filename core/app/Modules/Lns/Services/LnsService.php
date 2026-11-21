<?php

namespace App\Modules\Lns\Services;

/**
 * Provides LNS-related operations for the core banking app.
 *
 * The current implementation is a skeleton. Loan products, applications,
 * verification, analysis, approval, disbursement, and installment schedules
 * will be added later.
 */
class LnsService
{
    /**
     * Return a summary of the LNS module for diagnostics and module discovery.
     *
     * @return array{module: string, name: string, status: string}
     */
    public function summary(): array
    {
        return [
            'module' => 'LNS',
            'name' => 'Loan Management System',
            'status' => 'skeleton',
        ];
    }
}
