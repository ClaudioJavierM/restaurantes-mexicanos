<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Votos de usuarios para restaurantes
        if (!Schema::hasTable('restaurant_votes')) {
            Schema::create('restaurant_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('voter_ip', 45)->nullable();
                $table->string('voter_fingerprint')->nullable(); // Para evitar votos duplicados
                $table->year('year');
                $table->unsignedTinyInteger('month'); // 1-12
                $table->enum('vote_type', ['up', 'favorite', 'must_visit'])->default('up');
                $table->text('comment')->nullable();
                $table->boolean('is_verified')->default(false); // Si el usuario verificó su email
                $table->timestamps();
                
                // Un usuario solo puede votar una vez por restaurante por mes
                $table->unique(['restaurant_id', 'user_id', 'year', 'month'], 'unique_user_vote');
                $table->unique(['restaurant_id', 'voter_fingerprint', 'year', 'month'], 'unique_fingerprint_vote');
                $table->index(['restaurant_id', 'year', 'month']);
                $table->index(['year', 'month']);
            });
        }

        // Nominaciones de nuevos restaurantes
        if (!Schema::hasTable('restaurant_nominations')) {
            Schema::create('restaurant_nominations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('restaurant_name');
                $table->string('address')->nullable();
                $table->string('city');
                $table->string('state_code', 2);
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('google_maps_url')->nullable();
                $table->string('yelp_url')->nullable();
                $table->text('why_nominate')->nullable(); // Por qué lo nominan
                $table->string('nominator_name')->nullable();
                $table->string('nominator_email');
                $table->string('nominator_ip', 45)->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'duplicate'])->default('pending');
                $table->foreignId('created_restaurant_id')->nullable()->constrained('restaurants')->onDelete('set null');
                $table->text('admin_notes')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
                $table->index(['city', 'state_code']);
            });
        }

        // Rankings mensuales pre-calculados
        if (!Schema::hasTable('monthly_rankings')) {
            Schema::create('monthly_rankings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->year('year');
                $table->unsignedTinyInteger('month');
                $table->string('ranking_type'); // city, state, national
                $table->string('ranking_scope'); // city name, state code, 'usa'
                $table->integer('position');
                $table->integer('total_votes')->default(0);
                $table->integer('favorite_votes')->default(0);
                $table->decimal('monthly_score', 8, 2)->default(0);
                $table->decimal('cumulative_score', 10, 2)->default(0); // Puntos acumulados del año
                $table->string('badge_name')->nullable();
                $table->boolean('is_winner')->default(false); // Ganador del mes
                $table->timestamps();
                
                $table->unique(['restaurant_id', 'year', 'month', 'ranking_type', 'ranking_scope'], 'unique_monthly_ranking');
                $table->index(['year', 'month', 'ranking_type', 'ranking_scope', 'position'], 'monthly_rankings_idx');
            });
        }

        // Badges/Reconocimientos ganados
        if (!Schema::hasTable('restaurant_badges')) {
            Schema::create('restaurant_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('badge_type'); // monthly_winner, top_10, top_100, annual_winner, etc.
                $table->string('badge_scope'); // city, state, national
                $table->string('scope_name'); // Dallas, TX, USA
                $table->year('year');
                $table->unsignedTinyInteger('month')->nullable(); // null para badges anuales
                $table->integer('position')->nullable();
                $table->string('title'); // Restaurante del Mes - Dallas - Enero 2026
                $table->string('icon')->nullable(); // emoji o icono
                $table->string('color')->default('gold'); // gold, silver, bronze
                $table->string('certificate_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['restaurant_id', 'year']);
                $table->index(['badge_type', 'year', 'month']);
            });
        }

        // Suscripciones de restaurantes al programa FAMER
        if (!Schema::hasTable('famer_subscriptions')) {
            Schema::create('famer_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->year('year');
                $table->enum('status', ['active', 'pending', 'cancelled'])->default('pending');
                $table->boolean('wants_notifications')->default(true);
                $table->boolean('allows_promotion')->default(true);
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->text('goals')->nullable(); // Qué esperan lograr
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamps();
                
                $table->unique(['restaurant_id', 'year']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('famer_subscriptions');
        Schema::dropIfExists('restaurant_badges');
        Schema::dropIfExists('monthly_rankings');
        Schema::dropIfExists('restaurant_nominations');
        Schema::dropIfExists('restaurant_votes');
    }
};
