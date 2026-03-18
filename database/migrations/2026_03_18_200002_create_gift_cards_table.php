<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 16)->unique(); // XXXX-XXXX-XXXX-XXXX
            $table->decimal('initial_amount', 10, 2);
            $table->decimal('balance', 10, 2);
            // Purchaser info
            $table->string('purchaser_name');
            $table->string('purchaser_email');
            // Recipient info
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            // Status
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
