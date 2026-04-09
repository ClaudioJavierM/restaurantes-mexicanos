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
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->enum('event_type', ['boda', 'quinceañera', 'corporativo', 'cumpleaños', 'graduacion', 'otro'])->default('otro');
            $table->date('event_date')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->string('event_location')->nullable();
            $table->string('budget_range')->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'contacted', 'quoted', 'booked', 'declined'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->index(['restaurant_id', 'status']);
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_requests');
    }
};
