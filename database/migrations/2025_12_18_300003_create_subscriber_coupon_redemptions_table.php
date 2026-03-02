<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriber_coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_coupon_id')->constrained()->onDelete('cascade');
            $table->string('business_code', 50);
            $table->string('order_id', 100)->nullable();
            $table->decimal('order_total', 10, 2)->nullable();
            $table->decimal('discount_applied', 10, 2)->nullable();
            $table->string('customer_ip', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();
            
            // Un solo uso por cupón por negocio
            $table->unique(['subscriber_coupon_id', 'business_code'], 'unique_coupon_business');
            $table->index('business_code');
            $table->index('redeemed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriber_coupon_redemptions');
    }
};
