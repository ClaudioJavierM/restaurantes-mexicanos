<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->enum('reward_type', ['discount_percentage', 'discount_fixed', 'free_item', 'custom'])->default('discount_percentage');
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->string('free_item_name')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('redemption_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_active']);
            $table->index('points_required');
        });

        Schema::create('loyalty_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('restaurant_customers')->cascadeOnDelete();
            $table->foreignId('reward_id')->constrained('loyalty_rewards')->cascadeOnDelete();
            $table->integer('points_spent');
            $table->string('redemption_code')->unique();
            $table->enum('status', ['pending', 'used', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('redemption_code');
        });

        // Add loyalty settings to restaurants
        if (!Schema::hasColumn('restaurants', 'loyalty_enabled')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->boolean('loyalty_enabled')->default(false);
                $table->integer('points_per_dollar')->default(1);
                $table->integer('points_per_visit')->default(10);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_redemptions');
        Schema::dropIfExists('loyalty_rewards');
        
        if (Schema::hasColumn('restaurants', 'loyalty_enabled')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn(['loyalty_enabled', 'points_per_dollar', 'points_per_visit']);
            });
        }
    }
};
