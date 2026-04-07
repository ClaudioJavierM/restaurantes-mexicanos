<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('restaurant_customers')) {
            return;
        }

        Schema::table('restaurant_customers', function (Blueprint $table) {
            $table->json('cart_items')->nullable();
            $table->decimal('cart_total', 10, 2)->nullable();
            $table->timestamp('cart_updated_at')->nullable();
            $table->timestamp('cart_abandoned_at')->nullable();
            $table->boolean('cart_reminder_sent')->default(false);
            $table->timestamp('last_sms_sent_at')->nullable();
            $table->integer('sms_sends_count')->default(0);
            $table->timestamp('winback_sent_at')->nullable();
            $table->timestamp('birthday_sms_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_customers', function (Blueprint $table) {
            $table->dropColumn([
                'cart_items', 'cart_total', 'cart_updated_at', 'cart_abandoned_at',
                'cart_reminder_sent', 'last_sms_sent_at', 'sms_sends_count',
                'winback_sent_at', 'birthday_sms_sent_at',
            ]);
        });
    }
};
