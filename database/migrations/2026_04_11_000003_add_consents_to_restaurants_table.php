<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('email_consent')->default(true)->nullable()->after('owner_role');
            $table->boolean('sms_consent')->default(true)->nullable()->after('email_consent');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['email_consent', 'sms_consent']);
        });
    }
};
