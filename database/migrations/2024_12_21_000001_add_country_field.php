<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add country to states table
        if (!Schema::hasColumn('states', 'country')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('country', 2)->default('US')->after('code')->index();
            });
        }

        // Add country to restaurants table
        if (!Schema::hasColumn('restaurants', 'country')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->string('country', 2)->default('US')->after('state_id')->index();
            });
        }

        // Update all existing states to US
        DB::table('states')->update(['country' => 'US']);
        
        // Update all existing restaurants to US
        DB::table('restaurants')->update(['country' => 'US']);
    }

    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn('country');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
