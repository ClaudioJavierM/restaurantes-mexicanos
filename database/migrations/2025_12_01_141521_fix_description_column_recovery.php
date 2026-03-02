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
        // Check which columns exist
        $columns = Schema::getColumnListing('restaurants');
        $hasDescription = in_array('description', $columns);
        $hasDescriptionNew = in_array('description_new', $columns);

        // If description_new exists, we need to complete the migration
        if ($hasDescriptionNew) {
            // Copy any remaining data from description to description_new (if description still exists)
            if ($hasDescription) {
                $restaurants = DB::table('restaurants')
                    ->whereNotNull('description')
                    ->whereNull('description_new')
                    ->get();

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

                // Drop the old description column
                Schema::table('restaurants', function (Blueprint $table) {
                    $table->dropColumn('description');
                });
            }

            // Rename description_new to description
            Schema::table('restaurants', function (Blueprint $table) {
                $table->renameColumn('description_new', 'description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for recovery only, no need to reverse it
    }
};
