<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('famer_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            // Overall Score
            $table->unsignedTinyInteger('overall_score')->default(0); // 0-100
            $table->string('letter_grade', 2)->default('F'); // A+, A, A-, B+, B, B-, C+, C, C-, D+, D, D-, F

            // Category Scores (each 0-100, weighted in final calculation)
            $table->unsignedTinyInteger('profile_completeness_score')->default(0);
            $table->unsignedTinyInteger('online_presence_score')->default(0);
            $table->unsignedTinyInteger('customer_engagement_score')->default(0);
            $table->unsignedTinyInteger('menu_offerings_score')->default(0);
            $table->unsignedTinyInteger('mexican_authenticity_score')->default(0);
            $table->unsignedTinyInteger('digital_readiness_score')->default(0);

            // Detailed breakdown (JSON)
            $table->json('profile_breakdown')->nullable();
            $table->json('presence_breakdown')->nullable();
            $table->json('engagement_breakdown')->nullable();
            $table->json('menu_breakdown')->nullable();
            $table->json('authenticity_breakdown')->nullable();
            $table->json('digital_breakdown')->nullable();

            // Recommendations (JSON array)
            $table->json('recommendations')->nullable();

            // Comparison data
            $table->unsignedInteger('area_rank')->nullable();
            $table->unsignedInteger('area_total')->nullable();
            $table->unsignedInteger('category_rank')->nullable();
            $table->unsignedInteger('category_total')->nullable();
            $table->decimal('area_average', 5, 2)->nullable();
            $table->decimal('category_average', 5, 2)->nullable();

            // Metadata
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedTinyInteger('version')->default(1);

            $table->timestamps();

            $table->unique('restaurant_id');
            $table->index(['overall_score', 'letter_grade']);
            $table->index('calculated_at');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('famer_scores');
    }
};
