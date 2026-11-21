<?php

namespace App\Modules\Gls\Services;

/**
 * Provides GLS-related operations for the core banking app.
 *
 * The current implementation is a skeleton. Chart of accounts, automatic
 * journals, general ledger, and trial balance will be added later.
 */
class GlsService
{
    /**
     * Return a summary of the GLS module for diagnostics and module discovery.
     *
     * @return array{module: string, name: string, status: string}
     */
    public function summary(): array
    {
        return [
            'module' => 'GLS',
            'name' => 'General Ledger System',
            'status' => 'skeleton',
        ];
    }
}
