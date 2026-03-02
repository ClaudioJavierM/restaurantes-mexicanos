<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daily import statistics
        Schema::create("import_stats", function (Blueprint $table) {
            $table->id();
            $table->date("stat_date");
            $table->string("source")->default("yelp"); // yelp, google, foursquare, etc.
            $table->string("command")->nullable(); // artisan command name
            $table->integer("total_found")->default(0);
            $table->integer("imported")->default(0);
            $table->integer("duplicates_skipped")->default(0);
            $table->integer("errors")->default(0);
            $table->integer("api_calls")->default(0);
            $table->decimal("estimated_cost", 10, 4)->default(0);
            $table->json("states_processed")->nullable();
            $table->json("cities_processed")->nullable();
            $table->integer("duration_seconds")->nullable();
            $table->timestamps();

            $table->unique(["stat_date", "source", "command"]);
            $table->index("stat_date");
            $table->index("source");
        });

        // API call logs for detailed tracking
        Schema::create("api_call_logs", function (Blueprint $table) {
            $table->id();
            $table->string("service"); // yelp, google_places, foursquare, tripadvisor, apple_maps
            $table->string("endpoint"); // search, details, photos, etc.
            $table->integer("status_code")->nullable();
            $table->boolean("success")->default(true);
            $table->decimal("cost", 8, 6)->default(0);
            $table->json("params")->nullable();
            $table->string("error_message")->nullable();
            $table->timestamp("called_at");
            $table->timestamps();

            $table->index(["service", "called_at"]);
            $table->index("called_at");
        });

        // Enrichment tracking
        Schema::create("enrichment_stats", function (Blueprint $table) {
            $table->id();
            $table->date("stat_date");
            $table->string("type"); // seo_descriptions, yelp_backfill, email_scrape, etc.
            $table->integer("processed")->default(0);
            $table->integer("success")->default(0);
            $table->integer("failed")->default(0);
            $table->integer("skipped")->default(0);
            $table->decimal("api_cost", 10, 4)->default(0);
            $table->integer("duration_seconds")->nullable();
            $table->timestamps();

            $table->unique(["stat_date", "type"]);
            $table->index("stat_date");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("import_stats");
        Schema::dropIfExists("api_call_logs");
        Schema::dropIfExists("enrichment_stats");
    }
};
