<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add a new JSON column
        Schema::table('restaurants', function (Blueprint $table) {
            $table->json('description_new')->nullable();
        });

        // Step 2: Migrate existing text descriptions to JSON format
        // All existing descriptions will be set as English ('en')
        $restaurants = DB::table('restaurants')->whereNotNull('description')->get();

        foreach ($restaurants as $restaurant) {
            if (!empty($restaurant->description)) {
                $translatedDescription = json_encode([
                    'en' => $restaurant->description
                ], JSON_UNESCAPED_UNICODE);

                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update(['description_new' => $translatedDescription]);
            }
        }

        // Step 3: Drop the old description column
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // Step 4: Rename the new column to description (SQLite compatible)
        Schema::table('restaurants', function (Blueprint $table) {
            $table->renameColumn('description_new', 'description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add a new TEXT column
        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('description_old')->nullable();
        });

        // Step 2: Convert JSON back to plain text (extract English version)
        $restaurants = DB::table('restaurants')->whereNotNull('description')->get();

        foreach ($restaurants as $restaurant) {
            if (!empty($restaurant->description)) {
                $translations = json_decode($restaurant->description, true);
                $plainText = $translations['en'] ?? $translations['es'] ?? '';

                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update(['description_old' => $plainText]);
            }
        }

        // Step 3: Drop JSON column
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // Step 4: Rename text column back
        Schema::table('restaurants', function (Blueprint $table) {
            $table->renameColumn('description_old', 'description');
        });
    }
};
