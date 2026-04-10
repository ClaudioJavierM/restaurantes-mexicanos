<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gsc_performance', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('query', 500)->nullable();         // keyword
            $table->string('page', 500)->nullable();           // URL de la página
            $table->string('country', 2)->nullable();          // us, mx
            $table->string('device')->nullable();              // desktop, mobile, tablet
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);         // 0.0234 = 2.34%
            $table->decimal('position', 8, 2)->default(0);    // posición promedio
            $table->timestamps();
            $table->index(['date', 'country']);
            $table->index(['query']);
            $table->index(['page']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gsc_performance');
    }
};
