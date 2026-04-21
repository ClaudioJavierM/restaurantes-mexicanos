<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('nash_job_id')->nullable()->after('payment_intent_id');
            $table->string('nash_status')->nullable()->after('nash_job_id');
            $table->string('nash_provider')->nullable()->after('nash_status');
            $table->timestamp('nash_pickup_eta')->nullable()->after('nash_provider');
            $table->timestamp('nash_dropoff_eta')->nullable()->after('nash_pickup_eta');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'nash_job_id',
                'nash_status',
                'nash_provider',
                'nash_pickup_eta',
                'nash_dropoff_eta',
            ]);
        });
    }
};
