<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('test_name');

            // Variant A (control — current version)
            $table->string('variant_a_name');
            $table->text('variant_a_description')->nullable();
            $table->decimal('variant_a_price', 8, 2)->nullable();

            // Variant B (challenger)
            $table->string('variant_b_name');
            $table->text('variant_b_description')->nullable();
            $table->decimal('variant_b_price', 8, 2)->nullable();

            // Stats
            $table->unsignedInteger('views_a')->default(0);
            $table->unsignedInteger('views_b')->default(0);
            $table->unsignedInteger('orders_a')->default(0);
            $table->unsignedInteger('orders_b')->default(0);

            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->enum('winner', ['a', 'b', 'tie'])->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_ab_tests');
    }
};
