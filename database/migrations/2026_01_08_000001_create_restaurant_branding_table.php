<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_branding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            // App Branding
            $table->string('app_name')->nullable(); // Custom app name for PWA
            $table->string('app_short_name', 12)->nullable(); // Short name for PWA
            $table->string('app_description')->nullable();

            // Colors
            $table->string('primary_color', 7)->default('#dc2626'); // Hex color
            $table->string('secondary_color', 7)->default('#991b1b');
            $table->string('accent_color', 7)->default('#f59e0b');
            $table->string('text_color', 7)->default('#1f2937');
            $table->string('background_color', 7)->default('#ffffff');

            // Images
            $table->string('logo_url')->nullable();
            $table->string('icon_192_url')->nullable(); // PWA icon 192x192
            $table->string('icon_512_url')->nullable(); // PWA icon 512x512
            $table->string('splash_image_url')->nullable();
            $table->string('banner_image_url')->nullable();

            // PWA Settings
            $table->enum('display_mode', ['standalone', 'fullscreen', 'minimal-ui', 'browser'])->default('standalone');
            $table->enum('orientation', ['portrait', 'landscape', 'any'])->default('portrait');
            $table->string('theme_color', 7)->nullable();
            $table->string('background_splash_color', 7)->nullable();

            // Custom Domain (for Elite plan)
            $table->string('custom_domain')->nullable();
            $table->boolean('custom_domain_verified')->default(false);

            // White Label Features
            $table->boolean('hide_famer_branding')->default(false); // Elite only
            $table->boolean('custom_splash_screen')->default(false);
            $table->string('powered_by_text')->nullable(); // "Powered by FAMER" or custom

            // Social Links (for PWA footer)
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('twitter_url')->nullable();

            // Analytics
            $table->string('google_analytics_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index for fast lookups
            $table->unique('restaurant_id');
            $table->index('custom_domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_branding');
    }
};
