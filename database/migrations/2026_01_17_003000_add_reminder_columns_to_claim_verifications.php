<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claim_verifications', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->timestamp('approval_reminder_sent_at')->nullable();
            $table->integer('approval_reminder_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('claim_verifications', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'reminder_count', 'approval_reminder_sent_at', 'approval_reminder_count']);
        });
    }
};
