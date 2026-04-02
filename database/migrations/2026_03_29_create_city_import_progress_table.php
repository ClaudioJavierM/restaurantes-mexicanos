<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('city_import_progress')) {
            Schema::create('city_import_progress', function (Blueprint $table) {
                $table->id();
                $table->string('city');
                $table->string('state_code', 10);
                $table->integer('last_offset')->default(0);
                $table->integer('total_imported')->default(0);
                $table->integer('total_duplicates')->default(0);
                $table->decimal('last_duplicate_rate', 5, 2)->nullable();
                $table->boolean('is_exhausted')->default(false);
                $table->timestamp('last_import_at')->nullable();
                $table->timestamps();

                $table->unique(['city', 'state_code']);
                $table->index('is_exhausted');
                $table->index('state_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('city_import_progress');
    }
};
