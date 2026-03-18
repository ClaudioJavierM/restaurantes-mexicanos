<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', [
                'produce', 'meat', 'seafood', 'dairy', 'beverages',
                'dry_goods', 'supplies', 'other'
            ])->default('other');
            $table->string('unit')->default('kg'); // kg, L, pcs, lbs, oz, etc.
            $table->decimal('current_stock', 10, 3)->default(0);
            $table->decimal('min_stock', 10, 3)->default(0);
            $table->decimal('cost_per_unit', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'waste'])->default('in');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
    }
};
