<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "Mesa 1", "Terraza 3", "Barra"
            $table->string('table_code', 12)->unique(); // QR scan token
            $table->integer('capacity')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_active']);
        });

        Schema::create('table_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('restaurant_tables')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name')->nullable();
            $table->json('items'); // [{menu_item_id, name, quantity, price, notes}]
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])
                  ->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_orders');
        Schema::dropIfExists('restaurant_tables');
    }
};
