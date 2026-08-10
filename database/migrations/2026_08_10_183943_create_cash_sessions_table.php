<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('initial_cash_usd', 12, 2)->default(0.00);
            $table->decimal('initial_cash_ves', 14, 2)->default(0.00);
            $table->decimal('expected_cash_usd', 12, 2)->default(0.00);
            $table->decimal('expected_cash_ves', 14, 2)->default(0.00);
            $table->decimal('actual_cash_usd', 12, 2)->nullable();
            $table->decimal('actual_cash_ves', 14, 2)->nullable();
            $table->decimal('difference_usd', 12, 2)->default(0.00);
            $table->decimal('difference_ves', 14, 2)->default(0.00);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_sessions');
    }
};
