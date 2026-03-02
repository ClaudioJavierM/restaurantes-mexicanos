<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Nombre de quien reporta
            $table->string('email')->nullable(); // Email de quien reporta
            $table->string('issue_type'); // Tipo de problema
            $table->text('description'); // Descripción del problema
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->text('admin_notes')->nullable(); // Notas del admin
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('restaurant_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_reports');
    }
};
