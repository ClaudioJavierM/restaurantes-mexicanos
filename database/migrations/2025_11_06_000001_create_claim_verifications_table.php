<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('owner_name');
            $table->string('owner_email');
            $table->string('owner_phone');
            $table->enum('verification_method', ['email', 'phone', 'document', 'postcard']);
            $table->string('verification_code', 6)->nullable(); // 6-digit code
            $table->timestamp('code_sent_at')->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->integer('verification_attempts')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('document_path')->nullable(); // For document verification
            $table->enum('status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['restaurant_id', 'status']);
            $table->index(['owner_email']);
            $table->index(['verification_code', 'code_expires_at'], 'claim_verif_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_verifications');
    }
};
