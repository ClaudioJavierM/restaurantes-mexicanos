<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('transactional'); // transactional, campaign, notification
            $table->string('category')->nullable(); // reservation, claim, marketing, etc.
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('subject');
            $table->text('body_preview')->nullable(); // First 500 chars
            $table->string('mailable_class')->nullable();
            $table->string('template')->nullable();
            $table->json('metadata')->nullable(); // restaurant_id, order_id, etc.
            $table->string('status')->default('sent'); // queued, sent, delivered, opened, clicked, bounced, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->string('error_message')->nullable();
            $table->string('message_id')->nullable(); // Provider message ID
            $table->string('provider')->nullable(); // smtp, mailgun, ses, etc.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index(['type', 'created_at']);
            $table->index(['to_email', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
