<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_retentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('retention_number')->unique();
            $table->enum('type', ['iva_75', 'iva_100', 'islr'])->default('iva_75');
            $table->string('supplier_name');
            $table->string('supplier_tax_id'); // RIF
            $table->string('invoice_number');
            $table->decimal('base_amount_usd', 12, 2)->default(0.00);
            $table->decimal('tax_amount_usd', 12, 2)->default(0.00);
            $table->decimal('retained_amount_usd', 12, 2)->default(0.00);
            $table->date('retention_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_retentions');
    }
};
