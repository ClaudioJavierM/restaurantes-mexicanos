<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_customers', function (Blueprint $table) {
            $table->json('cart_items')->nullable()->after('metadata');
            $table->decimal('cart_total', 10, 2)->nullable()->after('cart_items');
            $table->timestamp('cart_updated_at')->nullable()->after('cart_total');
            $table->timestamp('cart_abandoned_at')->nullable()->after('cart_updated_at');
            $table->boolean('cart_reminder_sent')->default(false)->after('cart_abandoned_at');
            $table->timestamp('last_sms_sent_at')->nullable()->after('cart_reminder_sent');
            $table->integer('sms_sends_count')->default(0)->after('last_sms_sent_at');
            $table->timestamp('winback_sent_at')->nullable()->after('sms_sends_count');
            $table->timestamp('birthday_sms_sent_at')->nullable()->after('winback_sent_at');
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
