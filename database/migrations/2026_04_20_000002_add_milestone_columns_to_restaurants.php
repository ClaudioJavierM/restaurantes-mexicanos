<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Copy existing milestone_views_sent_at → milestone_50_sent_at, then drop old
            $table->timestamp('milestone_50_sent_at')->nullable()->after('milestone_views_sent_at');
            $table->timestamp('milestone_100_sent_at')->nullable()->after('milestone_50_sent_at');
            $table->timestamp('milestone_150_sent_at')->nullable()->after('milestone_100_sent_at');
        });

        // Migrate existing data from old column to new one
        \Illuminate\Support\Facades\DB::statement(
            'UPDATE restaurants SET milestone_50_sent_at = milestone_views_sent_at WHERE milestone_views_sent_at IS NOT NULL'
        );
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['milestone_50_sent_at', 'milestone_100_sent_at', 'milestone_150_sent_at']);
        });
    }
};
