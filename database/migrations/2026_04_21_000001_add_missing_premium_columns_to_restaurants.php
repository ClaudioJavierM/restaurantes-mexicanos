<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'premium_badge')) {
                $table->boolean('premium_badge')->default(false)->after('premium_email_marketing');
            }
            if (!Schema::hasColumn('restaurants', 'premium_menu')) {
                $table->boolean('premium_menu')->default(false)->after('premium_badge');
            }
            if (!Schema::hasColumn('restaurants', 'premium_reservations')) {
                $table->boolean('premium_reservations')->default(false)->after('premium_menu');
            }
            if (!Schema::hasColumn('restaurants', 'premium_chatbot')) {
                $table->boolean('premium_chatbot')->default(false)->after('premium_reservations');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['premium_badge', 'premium_menu', 'premium_reservations', 'premium_chatbot']);
        });
    }
};
