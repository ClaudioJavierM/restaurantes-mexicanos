<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_code'); // mf_imports, tormex, refrimex, etc.
            $table->timestamps();
            
            $table->unique(['user_id', 'business_code']);
            $table->index('business_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_businesses');
    }
};
