<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('cash_session_id')->nullable()->constrained('cash_sessions')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('sale_number')->unique();
            $table->enum('status', ['completed', 'pending_payment', 'cancelled'])->default('completed');
            $table->decimal('subtotal_usd', 12, 2)->default(0.00);
            $table->decimal('tax_usd', 12, 2)->default(0.00);
            $table->decimal('igtf_usd', 12, 2)->default(0.00);
            $table->decimal('discount_usd', 12, 2)->default(0.00);
            $table->decimal('total_usd', 12, 2)->default(0.00);
            $table->decimal('exchange_rate_bcv', 12, 4)->default(50.0000);
            $table->decimal('total_ves', 14, 2)->default(0.00);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
