<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->decimal('sentiment_score', 3, 2)->nullable()->after('status');
            $table->enum('sentiment_label', [
                'very_positive',
                'positive',
                'neutral',
                'negative',
                'very_negative',
            ])->nullable()->after('sentiment_score');
            $table->json('sentiment_keywords')->nullable()->after('sentiment_label');
            $table->timestamp('sentiment_analyzed_at')->nullable()->after('sentiment_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'sentiment_score',
                'sentiment_label',
                'sentiment_keywords',
                'sentiment_analyzed_at',
            ]);
        });
    }
};
