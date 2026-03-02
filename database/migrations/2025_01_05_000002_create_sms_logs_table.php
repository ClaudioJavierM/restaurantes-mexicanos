<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sms_automation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('restaurant_customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone');
            $table->text('message');
            $table->string('type')->default('automation');
            $table->string('trigger_type')->nullable();
            $table->string('status')->default('pending');
            $table->string('twilio_sid')->nullable();
            $table->string('error_message')->nullable();
            $table->string('short_url')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['restaurant_id', 'created_at']);
            $table->index(['phone', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('trigger_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
