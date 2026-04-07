<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Check-ins para Reviews Verificados
        if (!Schema::hasTable('check_ins')) {
            Schema::create('check_ins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->boolean('verified')->default(false);
                $table->integer('points_earned')->default(10);
                $table->timestamps();
                
                $table->index(['user_id', 'restaurant_id', 'created_at'], 'features_user_rest_idx');
            });
        }

        // 2. Sistema de Lealtad - Puntos
        if (!Schema::hasTable('loyalty_points')) {
            Schema::create('loyalty_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('points')->default(0);
                $table->string('level')->default('bronce'); // bronce, plata, oro, platino
                $table->integer('total_check_ins')->default(0);
                $table->integer('total_reviews')->default(0);
                $table->integer('total_referrals')->default(0);
                $table->timestamps();
                
                $table->unique('user_id');
            });
        }

        // 3. Transacciones de Puntos
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('points');
                $table->string('type'); // check_in, review, referral, redemption
                $table->string('description');
                $table->morphs('pointable'); // Para relacionar con check-in, review, etc.
                $table->timestamps();
            });
        }

        // 4. Ofertas Relampago / Flash Deals
        if (!Schema::hasTable('flash_deals')) {
            Schema::create('flash_deals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->text('description')->nullable();
                $table->text('description_en')->nullable();
                $table->string('discount_type'); // percentage, fixed, free_item
                $table->decimal('discount_value', 8, 2);
                $table->string('code')->unique();
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->integer('max_redemptions')->nullable();
                $table->integer('current_redemptions')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('applicable_for')->default('all'); // all, dine_in, takeout, delivery
                $table->timestamps();
                
                $table->index(['restaurant_id', 'is_active', 'starts_at', 'ends_at'], 'features_restaurant_idx');
            });
        }

        // 5. Programa de Referidos
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('referred_restaurant_id')->nullable()->constrained('restaurants')->onDelete('set null');
                $table->string('referral_code')->unique();
                $table->string('type'); // user, restaurant
                $table->string('status')->default('pending'); // pending, completed, rewarded
                $table->integer('reward_points')->default(0);
                $table->string('reward_type')->nullable(); // points, free_month, discount
                $table->timestamps();
            });
        }

        // 6. Eventos y Experiencias
        if (!Schema::hasTable('restaurant_events')) {
            Schema::create('restaurant_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->text('description')->nullable();
                $table->text('description_en')->nullable();
                $table->string('event_type'); // live_music, special_dinner, class, tasting, holiday
                $table->date('event_date');
                $table->time('start_time');
                $table->time('end_time')->nullable();
                $table->decimal('price', 8, 2)->nullable();
                $table->integer('capacity')->nullable();
                $table->integer('registered_count')->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['restaurant_id', 'event_date', 'is_active'], 'features_rest_date_idx');
            });
        }

        // 7. Registro de Eventos (asistentes)
        if (!Schema::hasTable('event_registrations')) {
            Schema::create('event_registrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('restaurant_events')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('guests')->default(1);
                $table->string('status')->default('confirmed'); // confirmed, cancelled, attended
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['event_id', 'user_id']);
            });
        }

        // 8. Widget Tokens para embeds
        if (!Schema::hasTable('widget_tokens')) {
            Schema::create('widget_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('token')->unique();
                $table->string('allowed_domain')->nullable();
                $table->json('settings')->nullable(); // theme, show_reviews, show_menu, etc.
                $table->boolean('is_active')->default(true);
                $table->integer('views')->default(0);
                $table->timestamps();
            });
        }

        // 9. Ordenes para Pickup/Takeout
        if (!Schema::hasTable('pickup_orders')) {
            Schema::create('pickup_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('order_number')->unique();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->string('customer_email')->nullable();
                $table->json('items'); // Array of items with name, qty, price
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('status')->default('pending'); // pending, confirmed, preparing, ready, picked_up, cancelled
                $table->dateTime('pickup_time');
                $table->text('special_instructions')->nullable();
                $table->string('payment_status')->default('pending'); // pending, paid, refunded
                $table->string('payment_method')->nullable();
                $table->timestamps();
                
                $table->index(['restaurant_id', 'status', 'pickup_time'], 'features_rest_status_idx');
            });
        }

        // Agregar campos a tablas existentes
        if (Schema::hasTable('reviews') && !Schema::hasColumn('reviews', 'is_verified')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->boolean('is_verified')->default(false)->after('status');
                $table->foreignId('check_in_id')->nullable()->after('is_verified');
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code')->nullable()->unique()->after('email');
                $table->foreignId('referred_by')->nullable()->after('referral_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_orders');
        Schema::dropIfExists('widget_tokens');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('restaurant_events');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('flash_deals');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('check_ins');
        
        if (Schema::hasColumn('reviews', 'is_verified')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn(['is_verified', 'check_in_id']);
            });
        }
        
        if (Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['referral_code', 'referred_by']);
            });
        }
    }
};
