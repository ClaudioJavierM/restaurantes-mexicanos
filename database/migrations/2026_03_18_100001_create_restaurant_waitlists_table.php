<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('party_size')->default(2);
            $table->string('special_request')->nullable();
            $table->enum('status', ['waiting', 'called', 'seated', 'no_show', 'cancelled'])
                  ->default('waiting');
            $table->integer('position')->nullable(); // queue position
            $table->integer('estimated_wait_minutes')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('seated_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_waitlists');
    }
};
