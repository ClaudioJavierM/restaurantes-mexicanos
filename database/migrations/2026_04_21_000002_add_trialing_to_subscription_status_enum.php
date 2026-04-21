<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE restaurants MODIFY COLUMN subscription_status ENUM('active','canceled','expired','past_due','trialing') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE restaurants MODIFY COLUMN subscription_status ENUM('active','canceled','expired','past_due') NULL");
    }
};
