<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // order, payment, alert, system
            $table->string('business'); // mf_imports, tormex, refrimex, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // additional data (order_id, amount, etc.)
            $table->string('severity')->default('info'); // info, warning, success, danger
            $table->string('link')->nullable(); // URL to the related item
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['business', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
