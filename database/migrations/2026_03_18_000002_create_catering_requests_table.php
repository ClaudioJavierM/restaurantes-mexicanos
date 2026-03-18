<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->date('event_date');
            $table->integer('guest_count');
            $table->string('event_type'); // boda, cumpleanos, corporativo, otro
            $table->string('event_location')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->enum('status', ['pending', 'viewed', 'quoted', 'accepted', 'declined', 'cancelled'])
                  ->default('pending');
            $table->text('owner_notes')->nullable();
            $table->decimal('quote_amount', 10, 2)->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_requests');
    }
};
