<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add trust/anti-fake fields to reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->tinyInteger('trust_score')->default(50)->after('not_helpful_count');
            $table->boolean('is_verified')->default(false)->after('trust_score');
            $table->json('trust_flags')->nullable()->after('is_verified');
            $table->string('visit_date')->nullable()->after('trust_flags');
            $table->enum('visit_type', ['dine_in', 'takeout', 'delivery'])->nullable()->after('visit_date');
            $table->integer('edit_count')->default(0)->after('visit_type');
            $table->timestamp('last_edited_at')->nullable()->after('edit_count');
            $table->boolean('flagged_suspicious')->default(false)->after('last_edited_at');
            $table->index('trust_score');
            $table->index('flagged_suspicious');
        });

        // Create review_edit_history audit trail table
        Schema::create('review_edit_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('old_comment');
            $table->text('new_comment');
            $table->string('old_title')->nullable();
            $table->string('new_title')->nullable();
            $table->tinyInteger('old_rating');
            $table->tinyInteger('new_rating');
            $table->string('edit_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('review_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_edit_history');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['trust_score']);
            $table->dropIndex(['flagged_suspicious']);
            $table->dropColumn([
                'trust_score', 'is_verified', 'trust_flags',
                'visit_date', 'visit_type', 'edit_count',
                'last_edited_at', 'flagged_suspicious',
            ]);
        });
    }
};
