<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table): void {
            $table->index('status', 'loans_status_index');
            $table->index('applied_at', 'loans_applied_at_index');
        });

        Schema::table('installments', function (Blueprint $table): void {
            $table->index('status', 'installments_status_index');
            $table->index('paid_at', 'installments_paid_at_index');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->index('paid_at', 'payments_paid_at_index');
        });

        Schema::table('journals', function (Blueprint $table): void {
            $table->index('date', 'journals_date_index');
        });

        Schema::table('journal_lines', function (Blueprint $table): void {
            $table->index('account_id', 'journal_lines_account_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('journal_lines', function (Blueprint $table): void {
            $table->dropIndex('journal_lines_account_id_index');
        });

        Schema::table('journals', function (Blueprint $table): void {
            $table->dropIndex('journals_date_index');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex('payments_paid_at_index');
        });

        Schema::table('installments', function (Blueprint $table): void {
            $table->dropIndex('installments_paid_at_index');
            $table->dropIndex('installments_status_index');
        });

        Schema::table('loans', function (Blueprint $table): void {
            $table->dropIndex('loans_applied_at_index');
            $table->dropIndex('loans_status_index');
        });
    }
};
