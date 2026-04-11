<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('owner_role')->nullable()->after('owner_phone');
        });
    }
    public function down(): void {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('owner_role');
        });
    }
};
