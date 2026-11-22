<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->unsignedInteger('min_tenor_months');
            $table->unsignedInteger('max_tenor_months');
            $table->decimal('interest_rate', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_product_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('tenor_months');
            $table->decimal('interest_rate', 5, 2);
            $table->string('status')->default('Diajukan');
            $table->decimal('outstanding', 15, 2)->default(0);
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decision');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('analyst_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('collectibility_ok')->default(false);
            $table->boolean('employment_ok')->default(false);
            $table->boolean('income_ok')->default(false);
            $table->boolean('credit_history_ok')->default(false);
            $table->text('survey_result')->nullable();
            $table->text('collateral')->nullable();
            $table->string('recommendation')->default('Belum Direkomendasikan');
            $table->timestamps();
        });

        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('level');
            $table->string('decision');
            $table->timestamps();
        });

        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('principal', 15, 2);
            $table->decimal('interest', 15, 2);
            $table->string('status')->default('Belum Lunas');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['loan_id', 'number']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamp('paid_at');
            $table->string('reference')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('installments');
        Schema::dropIfExists('loan_approvals');
        Schema::dropIfExists('loan_analyses');
        Schema::dropIfExists('loan_verifications');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_products');
    }
};
