<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->decimal('amount_usd', 10, 2);
            $table->decimal('exchange_rate_bcv', 10, 4)->nullable();
            $table->decimal('amount_ves', 12, 2)->nullable();
            $table->string('payment_method'); // pago_movil, zelle, binance_usdt, bank_transfer, cash_usd
            $table->string('reference_code');
            $table->date('payment_date');
            $table->string('plan_tier'); // starter, pro, enterprise
            $table->integer('months_paid')->default(1);
            $table->text('notes')->nullable();
            $table->string('receipt_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_payments');
    }
};
