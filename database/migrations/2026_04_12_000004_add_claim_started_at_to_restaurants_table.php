<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->timestamp('claim_started_at')->nullable()->after('onboarding_completed_at');
            $table->timestamp('claim_abandoned_sent_at')->nullable()->after('claim_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['claim_started_at', 'claim_abandoned_sent_at']);
        });
    }
};
