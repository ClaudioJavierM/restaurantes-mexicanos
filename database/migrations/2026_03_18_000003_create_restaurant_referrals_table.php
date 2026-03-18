<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add referral_code to restaurants
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('referral_code', 12)->nullable()->unique()->after('stripe_subscription_id');
        });

        // Referral tracking table
        Schema::create('restaurant_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('referred_restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->string('referral_code', 12);
            $table->string('referred_email')->nullable(); // email used when referral was made
            $table->enum('status', ['pending', 'claimed', 'subscribed', 'rewarded'])->default('pending');
            $table->enum('reward_type', ['discount_month', 'discount_percent', 'none'])->default('none');
            $table->decimal('reward_value', 8, 2)->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->index(['referral_code', 'status']);
            $table->index('referrer_restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_referrals');
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
    }
};
