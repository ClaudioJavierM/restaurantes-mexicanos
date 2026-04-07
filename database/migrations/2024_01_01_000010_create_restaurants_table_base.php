<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable("restaurants")) {
            Schema::create("restaurants", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
                $table->unsignedBigInteger("state_id")->nullable();
                $table->unsignedBigInteger("category_id")->nullable();
                $table->string("name");
                $table->string("slug")->unique();
                $table->text("description")->nullable();
                $table->string("email")->nullable();
                $table->string("phone")->nullable();
                $table->string("website")->nullable();
                $table->string("address")->nullable();
                $table->string("city")->nullable();
                $table->string("zip_code", 10)->nullable();
                $table->decimal("latitude", 10, 8)->nullable();
                $table->decimal("longitude", 11, 8)->nullable();
                $table->json("hours")->nullable();
                $table->enum("status", ["pending","approved","rejected"])->default("pending");
                $table->boolean("is_featured")->default(false);
                $table->boolean("is_active")->default(true);
                $table->decimal("average_rating", 3, 2)->default(0);
                $table->integer("total_reviews")->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
    public function down(): void { Schema::dropIfExists("restaurants"); }
};
