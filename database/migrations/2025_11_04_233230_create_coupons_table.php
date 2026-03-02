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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            // Coupon Information (Bilingual)
            $table->string('title'); // Spanish title
            $table->string('title_en')->nullable(); // English title
            $table->text('description')->nullable(); // Spanish description
            $table->text('description_en')->nullable(); // English description

            // Coupon Code & Discount
            $table->string('code')->unique(); // e.g., "TACO20", "SUMMER25"
            $table->enum('discount_type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // 20 for 20% or 5.00 for $5 off
            $table->decimal('minimum_purchase', 10, 2)->nullable(); // Min purchase required
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Max discount cap

            // Validity & Usage
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('usage_limit')->nullable(); // Total uses allowed (null = unlimited)
            $table->integer('usage_count')->default(0); // Times used
            $table->integer('usage_limit_per_user')->nullable(); // Uses per customer

            // Restrictions
            $table->json('applicable_days')->nullable(); // [1,2,3,4,5,6,7] for days of week
            $table->time('applicable_time_start')->nullable(); // e.g., "11:00"
            $table->time('applicable_time_end')->nullable(); // e.g., "14:00"
            $table->boolean('applicable_dine_in')->default(true);
            $table->boolean('applicable_takeout')->default(true);
            $table->boolean('applicable_delivery')->default(true);

            // Status & Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Show prominently
            $table->boolean('requires_subscription')->default(false); // Premium feature

            // Terms & Conditions
            $table->text('terms')->nullable(); // Spanish
            $table->text('terms_en')->nullable(); // English

            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('restaurant_id');
            $table->index('code');
            $table->index('valid_from');
            $table->index('valid_until');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
