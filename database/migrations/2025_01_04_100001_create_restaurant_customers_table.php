<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('source', ['manual', 'qr_code', 'reservation', 'order', 'website', 'import', 'referral'])->default('manual');
            $table->boolean('email_subscribed')->default(true);
            $table->boolean('sms_subscribed')->default(false);
            $table->integer('visits_count')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('points')->default(0);
            $table->timestamp('last_visit_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['restaurant_id', 'email']);
            $table->index(['restaurant_id', 'email_subscribed'], 'cust_rest_email_idx');
            $table->index(['restaurant_id', 'source']);
            $table->index('birthday');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_customers');
    }
};
