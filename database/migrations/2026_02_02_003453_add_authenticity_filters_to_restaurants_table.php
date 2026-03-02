<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Authenticity filters for Mexican restaurants
     * These help identify truly authentic Mexican restaurants
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Bebidas Tradicionales
            if (!Schema::hasColumn('restaurants', 'has_cafe_de_olla')) {
                $table->boolean('has_cafe_de_olla')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_fresh_tortillas')) {
                $table->boolean('has_fresh_tortillas')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_handmade_tortillas')) {
                $table->boolean('has_handmade_tortillas')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_aguas_frescas')) {
                $table->boolean('has_aguas_frescas')->default(false);
            }

            // Salsas y Preparaciones
            if (!Schema::hasColumn('restaurants', 'has_homemade_salsa')) {
                $table->boolean('has_homemade_salsa')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_homemade_mole')) {
                $table->boolean('has_homemade_mole')->default(false);
            }

            // Métodos de Cocción Tradicionales
            if (!Schema::hasColumn('restaurants', 'has_charcoal_grill')) {
                $table->boolean('has_charcoal_grill')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_comal')) {
                $table->boolean('has_comal')->default(false);
            }

            // Platillos Tradicionales
            if (!Schema::hasColumn('restaurants', 'has_birria')) {
                $table->boolean('has_birria')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_carnitas')) {
                $table->boolean('has_carnitas')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_pozole_menudo')) {
                $table->boolean('has_pozole_menudo')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_barbacoa')) {
                $table->boolean('has_barbacoa')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_tamales')) {
                $table->boolean('has_tamales')->default(false);
            }

            // Panadería y Postres
            if (!Schema::hasColumn('restaurants', 'has_pan_dulce')) {
                $table->boolean('has_pan_dulce')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_churros')) {
                $table->boolean('has_churros')->default(false);
            }

            // Bebidas Alcohólicas
            if (!Schema::hasColumn('restaurants', 'has_mezcal_tequila')) {
                $table->boolean('has_mezcal_tequila')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_micheladas')) {
                $table->boolean('has_micheladas')->default(false);
            }

            // Extras de Autenticidad
            if (!Schema::hasColumn('restaurants', 'has_mexican_candy')) {
                $table->boolean('has_mexican_candy')->default(false);
            }
            if (!Schema::hasColumn('restaurants', 'has_imported_products')) {
                $table->boolean('has_imported_products')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $columns = [
                'has_cafe_de_olla',
                'has_fresh_tortillas',
                'has_handmade_tortillas',
                'has_aguas_frescas',
                'has_homemade_salsa',
                'has_homemade_mole',
                'has_charcoal_grill',
                'has_comal',
                'has_birria',
                'has_carnitas',
                'has_pozole_menudo',
                'has_barbacoa',
                'has_tamales',
                'has_pan_dulce',
                'has_churros',
                'has_mezcal_tequila',
                'has_micheladas',
                'has_mexican_candy',
                'has_imported_products',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('restaurants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
