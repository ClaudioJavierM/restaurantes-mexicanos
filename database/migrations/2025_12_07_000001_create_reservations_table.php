<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Guest info (for non-authenticated users)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            // Reservation details
            $table->date('reservation_date');
            $table->string('reservation_time'); // e.g., "19:00", "19:30"
            $table->integer('party_size')->default(2);
            $table->text('special_requests')->nullable();
            $table->string('occasion')->default('none');

            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Tracking
            $table->string('confirmation_code', 8)->unique();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->string('ip_address')->nullable();

            // Owner notes
            $table->text('internal_notes')->nullable();
            $table->string('table_assigned')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['restaurant_id', 'reservation_date']);
            $table->index(['restaurant_id', 'status']);
            $table->index('confirmation_code');
            $table->index('user_id');
            $table->index('reservation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
