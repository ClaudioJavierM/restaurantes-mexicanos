<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->unsignedTinyInteger('onboarding_step')->default(0)->after('unclaimed_coupon_sent_at'); // 0=not started, 1-5=step completed, 6=done
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['onboarding_step', 'onboarding_completed_at']);
        });
    }
};
