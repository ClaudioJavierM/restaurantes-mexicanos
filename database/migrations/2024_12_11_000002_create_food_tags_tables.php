<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de tags de comida
        Schema::create('food_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla pivote
        Schema::create('restaurant_food_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['restaurant_id', 'food_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_food_tag');
        Schema::dropIfExists('food_tags');
    }
};
