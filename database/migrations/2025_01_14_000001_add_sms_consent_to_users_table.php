<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('sms_marketing_consent')->default(false)->after('phone');
            $table->timestamp('sms_consent_at')->nullable()->after('sms_marketing_consent');
            $table->timestamp('sms_opted_out_at')->nullable()->after('sms_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sms_marketing_consent', 'sms_consent_at', 'sms_opted_out_at']);
        });
    }
};
