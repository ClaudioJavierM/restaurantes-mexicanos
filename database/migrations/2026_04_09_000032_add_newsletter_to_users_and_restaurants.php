<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('newsletter_subscribed')->default(false)->after('sms_opted_out_at');
            $table->timestamp('newsletter_subscribed_at')->nullable()->after('newsletter_subscribed');
            $table->unsignedInteger('listmonk_subscriber_id')->nullable()->after('newsletter_subscribed_at');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('owner_newsletter')->default(false)->after('owner_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'newsletter_subscribed',
                'newsletter_subscribed_at',
                'listmonk_subscriber_id',
            ]);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('owner_newsletter');
        });
    }
};
