<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mexican_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Agregar foreign key a restaurants
        Schema::table('restaurants', function (Blueprint $table) {
            $table->foreignId('mexican_region_id')->nullable()->after('mexican_region')->constrained('mexican_regions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeign(['mexican_region_id']);
            $table->dropColumn('mexican_region_id');
        });
        Schema::dropIfExists('mexican_regions');
    }
};
