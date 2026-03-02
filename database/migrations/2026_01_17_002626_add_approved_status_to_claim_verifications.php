<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claim_verifications', function (Blueprint $table) {
            $table->string('status_new')->default('pending')->after('status');
        });
        
        DB::table('claim_verifications')->update([
            'status_new' => DB::raw('status')
        ]);
        
        Schema::table('claim_verifications', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('claim_verifications', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void {}
};
