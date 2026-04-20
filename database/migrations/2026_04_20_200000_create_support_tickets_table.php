<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // FAMER-XXXXX format
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('restaurant_name')->nullable();
            $table->string('restaurant_slug')->nullable();
            $table->enum('issue_type', ['verification', 'claim', 'subscription', 'data_error', 'other']);
            $table->text('message');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->string('source')->default('carmen_widget'); // carmen_widget, email, etc.
            $table->json('context')->nullable(); // extra data like claim step, URL, etc.
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
