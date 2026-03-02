<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // LAUNCH50, BLACKFRIDAY, etc
            $table->string('name'); // Human readable name
            $table->text('description')->nullable();

            // Discount type
            $table->enum('discount_type', ['percentage', 'fixed']); // percentage or fixed amount
            $table->decimal('discount_value', 10, 2); // 50 for 50% or 20 for $20

            // Duration
            $table->enum('duration', ['once', 'repeating', 'forever'])->default('once');
            $table->integer('duration_in_months')->nullable(); // For repeating

            // Limits
            $table->integer('max_redemptions')->nullable(); // Max total uses
            $table->integer('times_redeemed')->default(0); // Current uses
            $table->timestamp('expires_at')->nullable();

            // Stripe IDs
            $table->string('stripe_coupon_id')->nullable();
            $table->string('stripe_promotion_code_id')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->string('created_by')->nullable(); // Admin user who created it
            $table->json('metadata')->nullable(); // Extra data

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_coupons');
    }
};
