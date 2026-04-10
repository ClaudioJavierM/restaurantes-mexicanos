<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('checkins')) {
            Schema::create('checkins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                      ->constrained('users')
                      ->cascadeOnDelete();
                $table->foreignId('restaurant_id')
                      ->constrained('restaurants')
                      ->cascadeOnDelete();
                $table->date('visited_at');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->unique(
                    ['user_id', 'restaurant_id', 'visited_at'],
                    'checkins_user_restaurant_day_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
