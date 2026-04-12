<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->timestamp('unclaimed_stats_sent_at')->nullable()->after('famer_email_3_sent_at');
            $table->timestamp('unclaimed_coupon_sent_at')->nullable()->after('unclaimed_stats_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['unclaimed_stats_sent_at', 'unclaimed_coupon_sent_at']);
        });
    }
};
