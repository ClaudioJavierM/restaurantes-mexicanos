<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('owner_response')->nullable()->after('comment');
            $table->foreignId('owner_response_by')->nullable()->after('owner_response')->constrained('users');
            $table->timestamp('owner_response_at')->nullable()->after('owner_response_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['owner_response_by']);
            $table->dropColumn(['owner_response', 'owner_response_by', 'owner_response_at']);
        });
    }
};
