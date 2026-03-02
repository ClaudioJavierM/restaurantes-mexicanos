<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mf_imports_promo_codes', function ($table) {
            $table->id();
            
            // MF Imports customer info
            $table->string('mf_customer_email');
            $table->string('mf_customer_name')->nullable();
            $table->string('mf_order_id')->unique();
            $table->decimal('mf_order_total', 12, 2);
            
            // Promo code details
            $table->string('promo_code')->unique();
            $table->string('stripe_promotion_code_id')->nullable();
            $table->enum('tier', ['3_months', '6_months']);
            $table->boolean('used_famer_discount_on_order')->default(false);
            
            // Usage tracking
            $table->boolean('is_redeemed')->default(false);
            $table->timestamp('redeemed_at')->nullable();
            $table->unsignedBigInteger('redeemed_by_restaurant_id')->nullable();
            
            // Status
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('mf_customer_email');
            $table->index('promo_code');
            $table->index(['is_redeemed', 'is_active']);
            
            $table->foreign('redeemed_by_restaurant_id')->references('id')->on('restaurants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mf_imports_promo_codes');
    }
};
