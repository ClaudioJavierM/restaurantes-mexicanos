<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("calls", function (Blueprint $table) {
            $table->id();
            $table->string("elevenlabs_call_id")->unique();
            $table->string("agent_id")->nullable();
            $table->string("caller_phone")->nullable();
            $table->string("direction")->default("inbound");
            $table->string("status")->nullable();
            $table->text("transcript")->nullable();
            $table->text("summary")->nullable();
            $table->string("category")->nullable();
            $table->integer("duration_seconds")->nullable();
            $table->json("metadata")->nullable();
            $table->timestamp("call_started_at")->nullable();
            $table->timestamp("call_ended_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("calls");
    }
};
