<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->enum('payment_method', ['cash_usd', 'cash_ves', 'pago_movil', 'zelle', 'transfer_ves', 'card', 'binance'])->default('cash_usd');
            $table->enum('currency', ['USD', 'VES'])->default('USD');
            $table->decimal('amount_usd', 12, 2)->default(0.00);
            $table->decimal('amount_native', 14, 2)->default(0.00);
            $table->decimal('exchange_rate', 12, 4)->default(50.0000);
            $table->string('reference_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
