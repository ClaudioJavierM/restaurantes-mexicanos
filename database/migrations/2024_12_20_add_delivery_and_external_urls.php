<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Delivery platforms
            if (!Schema::hasColumn('restaurants', 'doordash_url')) {
                $table->string('doordash_url')->nullable()->after('order_url');
            }
            if (!Schema::hasColumn('restaurants', 'ubereats_url')) {
                $table->string('ubereats_url')->nullable()->after('doordash_url');
            }
            if (!Schema::hasColumn('restaurants', 'grubhub_url')) {
                $table->string('grubhub_url')->nullable()->after('ubereats_url');
            }
            if (!Schema::hasColumn('restaurants', 'postmates_url')) {
                $table->string('postmates_url')->nullable()->after('grubhub_url');
            }
            if (!Schema::hasColumn('restaurants', 'seamless_url')) {
                $table->string('seamless_url')->nullable()->after('postmates_url');
            }
            if (!Schema::hasColumn('restaurants', 'caviar_url')) {
                $table->string('caviar_url')->nullable()->after('seamless_url');
            }

            // Review platforms
            if (!Schema::hasColumn('restaurants', 'tripadvisor_url')) {
                $table->string('tripadvisor_url')->nullable()->after('facebook_url');
            }
            if (!Schema::hasColumn('restaurants', 'opentable_url')) {
                $table->string('opentable_url')->nullable()->after('tripadvisor_url');
            }
            if (!Schema::hasColumn('restaurants', 'resy_url')) {
                $table->string('resy_url')->nullable()->after('opentable_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $columns = [
                'doordash_url', 'ubereats_url', 'grubhub_url',
                'postmates_url', 'seamless_url', 'caviar_url',
                'tripadvisor_url', 'opentable_url', 'resy_url'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('restaurants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
