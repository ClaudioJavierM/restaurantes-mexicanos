<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_campaign_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // birthday, anniversary, reactivation, welcome
            $table->boolean('is_active')->default(false);
            $table->string('subject');
            $table->text('message');
            $table->integer('send_days_before')->default(0); // 0 = on the day, 1 = day before
            $table->string('coupon_code')->nullable();
            $table->integer('coupon_discount_percent')->nullable();
            $table->integer('coupon_valid_days')->default(7);
            $table->timestamp('last_run_at')->nullable();
            $table->integer('total_sent')->default(0);
            $table->timestamps();

            $table->unique(['restaurant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_campaign_configs');
    }
};
