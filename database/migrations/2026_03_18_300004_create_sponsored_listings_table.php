<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsored_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->enum('placement', ['homepage_featured', 'search_top', 'city_spotlight'])
                  ->default('homepage_featured');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsored_listings');
    }
};
