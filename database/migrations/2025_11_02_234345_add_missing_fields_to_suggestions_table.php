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
        Schema::table('suggestions', function (Blueprint $table) {
            $table->string('submitter_phone')->nullable()->after('submitter_email');
            $table->string('restaurant_zip_code')->nullable()->after('restaurant_state');
            $table->foreignId('category_id')->nullable()->after('restaurant_website')->constrained()->nullOnDelete();
            $table->text('description')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['submitter_phone', 'restaurant_zip_code', 'category_id', 'description']);
        });
    }
};
