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
        Schema::table('restaurants', function (Blueprint $table) {
            // Google Places Integration
            $table->string('google_place_id')->nullable()->after('slug');
            $table->text('google_maps_url')->nullable()->after('google_place_id');

            // Business Status
            $table->enum('business_status', [
                'operational',      // Operando normalmente
                'temporarily_closed', // Cerrado temporalmente
                'permanently_closed', // Cerrado permanentemente
                'coming_soon'       // Próxima apertura
            ])->default('operational')->after('is_active');

            $table->date('opening_date')->nullable()->after('business_status'); // Para "coming soon"

            // Google verification
            $table->timestamp('last_google_verification')->nullable()->after('opening_date');
            $table->boolean('google_verified')->default(false)->after('last_google_verification');

            // Additional Google data
            $table->decimal('google_rating', 3, 2)->nullable()->after('average_rating');
            $table->integer('google_reviews_count')->default(0)->after('google_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'google_place_id',
                'google_maps_url',
                'business_status',
                'opening_date',
                'last_google_verification',
                'google_verified',
                'google_rating',
                'google_reviews_count',
            ]);
        });
    }
};
