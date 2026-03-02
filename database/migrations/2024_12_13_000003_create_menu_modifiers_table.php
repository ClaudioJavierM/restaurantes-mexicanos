<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Grupos de modificadores (ej: "Tamaño", "Extras", "Proteína")
        Schema::create('menu_modifier_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_es')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('min_selections')->default(0);
            $table->integer('max_selections')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Opciones de modificadores
        Schema::create('menu_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modifier_group_id')->constrained('menu_modifier_groups')->onDelete('cascade');
            $table->string('name');
            $table->string('name_es')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_modifiers');
        Schema::dropIfExists('menu_modifier_groups');
    }
};
