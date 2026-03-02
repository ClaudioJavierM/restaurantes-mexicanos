<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('has_cafe_de_olla')->default(false)->after('online_ordering');
            $table->boolean('has_fresh_tortillas')->default(false)->after('has_cafe_de_olla');
            $table->boolean('has_handmade_tortillas')->default(false)->after('has_fresh_tortillas');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['has_cafe_de_olla', 'has_fresh_tortillas', 'has_handmade_tortillas']);
        });
    }
};
