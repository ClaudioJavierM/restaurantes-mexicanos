<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('restaurant_votes')) {
            return;
        }

        Schema::table('restaurant_votes', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_votes', 'voter_email')) {
                $table->string('voter_email')->nullable();
                $table->index(['restaurant_id', 'voter_email', 'year', 'month'], 'votes_rest_email_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_votes', function (Blueprint $table) {
            $table->dropColumn('voter_email');
        });
    }
};
