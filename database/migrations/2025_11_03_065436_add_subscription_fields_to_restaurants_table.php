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
        Schema::table('restaurants', function (Blueprint $table) {
            // Ownership & Claim
            $table->boolean('is_claimed')->default(false)->after('status');
            $table->timestamp('claimed_at')->nullable()->after('is_claimed');
            $table->string('claim_token')->nullable()->unique()->after('claimed_at');
            $table->string('verification_method')->nullable()->after('claim_token'); // phone, email, document

            // Subscription
            $table->enum('subscription_tier', ['free', 'claimed', 'premium', 'elite'])->default('free')->after('verification_method');
            $table->string('stripe_customer_id')->nullable()->after('subscription_tier');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->timestamp('subscription_started_at')->nullable()->after('stripe_subscription_id');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_started_at');
            $table->enum('subscription_status', ['active', 'canceled', 'expired', 'past_due'])->nullable()->after('subscription_expires_at');

            // Premium Features Access
            $table->boolean('premium_analytics')->default(false)->after('subscription_status');
            $table->boolean('premium_seo')->default(false)->after('premium_analytics');
            $table->boolean('premium_featured')->default(false)->after('premium_seo');
            $table->boolean('premium_coupons')->default(false)->after('premium_featured');
            $table->boolean('premium_email_marketing')->default(false)->after('premium_coupons');

            // Analytics (for premium users)
            $table->integer('profile_views')->default(0)->after('premium_email_marketing');
            $table->integer('phone_clicks')->default(0)->after('profile_views');
            $table->integer('website_clicks')->default(0)->after('phone_clicks');
            $table->integer('direction_clicks')->default(0)->after('website_clicks');

            // Owner info
            $table->string('owner_name')->nullable()->after('direction_clicks');
            $table->string('owner_email')->nullable()->after('owner_name');
            $table->string('owner_phone')->nullable()->after('owner_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'is_claimed',
                'claimed_at',
                'claim_token',
                'verification_method',
                'subscription_tier',
                'stripe_customer_id',
                'stripe_subscription_id',
                'subscription_started_at',
                'subscription_expires_at',
                'subscription_status',
                'premium_analytics',
                'premium_seo',
                'premium_featured',
                'premium_coupons',
                'premium_email_marketing',
                'profile_views',
                'phone_clicks',
                'website_clicks',
                'direction_clicks',
                'owner_name',
                'owner_email',
                'owner_phone',
            ]);
        });
    }
};
