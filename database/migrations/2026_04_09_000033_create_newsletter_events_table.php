<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('newsletter_events')) return;

        Schema::create('newsletter_events', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('listmonk'); // listmonk, resend
            $table->string('event_type');                  // subscribed, unsubscribed, sent, delivered, opened, clicked, bounced, complained
            $table->string('email');
            $table->string('name')->nullable();
            $table->unsignedInteger('subscriber_id')->nullable();   // Listmonk subscriber ID
            $table->unsignedInteger('campaign_id')->nullable();     // Listmonk campaign ID
            $table->string('campaign_name')->nullable();
            $table->string('link_url')->nullable();                 // for click events
            $table->string('message_id')->nullable();               // Resend/SMTP message ID
            $table->string('list_name')->nullable();                // which list they subscribed to
            $table->json('raw_payload')->nullable();                // full webhook payload for debugging
            $table->timestamp('occurred_at')->nullable();           // when the event happened
            $table->timestamps();

            $table->index(['email', 'event_type']);
            $table->index(['source', 'event_type', 'created_at']);
            $table->index(['campaign_id', 'event_type']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_events');
    }
};
