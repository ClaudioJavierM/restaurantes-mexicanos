<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_benefits', function (Blueprint $table) {
            $table->id();
            $table->enum('tier', ['free', 'premium', 'elite']);
            $table->string('business_code', 50);
            $table->string('business_name', 100);
            $table->string('business_url', 255)->nullable();
            $table->string('business_logo', 255)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'free_shipping']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('includes_free_shipping')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['tier', 'business_code'], 'unique_tier_business');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_benefits');
    }
};
