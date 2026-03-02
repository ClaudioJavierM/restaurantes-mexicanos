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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Info del sugerido
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->string('restaurant_name');
            $table->text('restaurant_address');
            $table->string('restaurant_city');
            $table->string('restaurant_state');
            $table->string('restaurant_phone')->nullable();
            $table->string('restaurant_website')->nullable();
            $table->text('notes')->nullable(); // Por qué lo sugiere

            // Moderación
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Notas del admin

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
