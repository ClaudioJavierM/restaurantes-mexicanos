<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título del anuncio
            $table->text('description')->nullable(); // Descripción del anuncio
            $table->string('link_url'); // URL a donde redirige
            $table->string('button_text')->default('Ver más'); // Texto del botón
            $table->string('placement')->default('sidebar'); // Ubicación: sidebar, footer, etc
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete(); // Para anuncios específicos por estado
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0); // Orden de visualización
            $table->date('starts_at')->nullable(); // Fecha de inicio
            $table->date('ends_at')->nullable(); // Fecha de fin
            $table->integer('clicks_count')->default(0); // Contador de clicks
            $table->integer('views_count')->default(0); // Contador de vistas
            $table->timestamps();

            $table->index('is_active');
            $table->index('placement');
            $table->index('state_id');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
