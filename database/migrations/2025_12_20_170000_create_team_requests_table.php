<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_phone')->nullable();
            $table->enum('request_type', ['team_join', 'ownership_dispute'])->default('team_join');
            $table->enum('requested_role', ['admin', 'manager', 'editor', 'viewer'])->default('manager');
            $table->text('message')->nullable();
            $table->text('evidence_urls')->nullable(); // For disputes - JSON array of document URLs
            $table->enum('status', ['pending', 'approved', 'rejected', 'disputed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->string('token', 64)->unique(); // For email verification/actions
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
            $table->index(['requester_email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_requests');
    }
};
