<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('famer_email_3_sent_at');
            $table->tinyInteger('onboarding_step')->default(0)->after('onboarding_completed');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed', 'onboarding_step', 'onboarding_completed_at']);
        });
    }
};
