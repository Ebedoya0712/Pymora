<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->default('Unidad');
            $table->decimal('cost_usd', 12, 4)->default(0.0000);
            $table->decimal('price_usd', 12, 4)->default(0.0000);
            $table->decimal('tax_rate', 5, 2)->default(16.00); // IVA %
            $table->boolean('has_variants')->default(false);
            $table->boolean('has_lots')->default(false);
            $table->boolean('is_recipe')->default(false);
            $table->decimal('min_stock_alert', 10, 2)->default(5.00);
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
