<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->enum('placement_type', ['city', 'state', 'national'])->default('city');
            $table->string('scope')->nullable(); // city name, state code, or 'usa'
            $table->date('starts_at');
            $table->date('ends_at');
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->enum('status', ['active', 'pending', 'expired', 'cancelled'])->default('pending');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->timestamps();
            $table->index(['placement_type', 'scope', 'status', 'starts_at', 'ends_at']);
            $table->index(['restaurant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_placements');
    }
};
