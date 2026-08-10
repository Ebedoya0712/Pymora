<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rif_tax_id')->nullable();
            $table->string('subdomain')->unique();
            $table->enum('plan_tier', ['trial', 'starter', 'pro', 'enterprise'])->default('starter');
            $table->boolean('is_active')->default(true);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('currency_primary')->default('USD');
            $table->string('currency_secondary')->default('VES');
            $table->decimal('bcv_rate', 12, 4)->default(50.0000);
            $table->decimal('parallel_rate', 12, 4)->default(52.0000);
            $table->decimal('igtf_percentage', 5, 2)->default(3.00);
            $table->string('logo_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
