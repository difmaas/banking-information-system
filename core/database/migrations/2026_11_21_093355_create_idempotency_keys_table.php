<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('key');
            $table->string('method');
            $table->text('path');
            $table->unsignedSmallInteger('status_code');
            $table->json('response');
            $table->timestamps();

            $table->unique(['service', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
