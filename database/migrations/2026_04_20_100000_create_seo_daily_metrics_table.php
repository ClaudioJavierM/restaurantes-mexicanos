<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seo_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            // GSC data
            $table->integer('gsc_clicks')->default(0);
            $table->integer('gsc_impressions')->default(0);
            $table->float('gsc_ctr')->default(0);
            $table->float('gsc_position')->default(0);
            $table->integer('gsc_indexed_pages')->nullable();
            // Umami / traffic
            $table->integer('total_sessions')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->integer('page_views')->default(0);
            // FAMER business metrics
            $table->integer('new_claims')->default(0);
            $table->integer('total_claimed')->default(0);
            $table->integer('new_premium')->default(0);
            $table->integer('total_premium')->default(0);
            $table->integer('emails_sent')->default(0);
            $table->integer('restaurants_with_email')->default(0);
            // Rankings snapshot
            $table->json('top_keywords')->nullable(); // [{keyword, position, clicks}]
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('seo_daily_metrics');
    }
};
