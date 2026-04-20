<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Stores "2026-04" format — resets automatically each new month
            $table->string('monthly_milestone_50_month', 7)->nullable()->after('milestone_150_sent_at');
            $table->string('monthly_milestone_100_month', 7)->nullable()->after('monthly_milestone_50_month');
            $table->string('monthly_milestone_150_month', 7)->nullable()->after('monthly_milestone_100_month');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['monthly_milestone_50_month', 'monthly_milestone_100_month', 'monthly_milestone_150_month']);
        });
    }
};
