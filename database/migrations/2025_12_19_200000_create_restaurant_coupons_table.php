<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'bogo', 'free_item'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->nullable(); // For percentage or fixed amount
            $table->string('free_item_name')->nullable(); // For free_item type
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->integer('max_uses')->nullable(); // Total uses allowed
            $table->integer('uses_count')->default(0);
            $table->integer('max_uses_per_user')->default(1);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->json('valid_days')->nullable(); // Days of week it's valid
            $table->time('valid_time_start')->nullable();
            $table->time('valid_time_end')->nullable();
            $table->string('terms')->nullable(); // Short terms
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_profile')->default(true); // Show on restaurant page
            $table->timestamps();
            
            $table->index(['restaurant_id', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_coupons');
    }
};
