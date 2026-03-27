<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_type');
            $table->text('message_template');
            $table->integer('delay_minutes')->default(15);
            $table->json('conditions')->nullable();
            $table->string('coupon_code')->nullable();
            $table->integer('coupon_discount')->nullable();
            $table->string('coupon_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sends_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->integer('conversions_count')->default(0);
            $table->decimal('revenue_generated', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index(['restaurant_id', 'trigger_type', 'is_active'], 'sms_auto_rest_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_automations');
    }
};
