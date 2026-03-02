<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_votes', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_votes', 'voter_email')) {
                $table->string('voter_email')->nullable()->after('voter_fingerprint');
                $table->index(['restaurant_id', 'voter_email', 'year', 'month']);
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
