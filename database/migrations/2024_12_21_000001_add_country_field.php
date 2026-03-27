<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add country to states table (only if states table exists)
        if (Schema::hasTable('states') && !Schema::hasColumn('states', 'country')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('country', 2)->default('US')->index();
            });
            DB::table('states')->update(['country' => 'US']);
        }

        // Add country to restaurants table
        if (!Schema::hasColumn('restaurants', 'country')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->string('country', 2)->default('US')->index();
            });
            DB::table('restaurants')->update(['country' => 'US']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('states') && Schema::hasColumn('states', 'country')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }

        if (Schema::hasColumn('restaurants', 'country')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }
};
