<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->enum('reason', ['unsubscribed', 'bounced', 'complained', 'manual']);
            $table->string('source')->nullable(); // 'resend_webhook', 'user_request', 'admin'
            $table->timestamp('suppressed_at');
            $table->timestamps();
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
