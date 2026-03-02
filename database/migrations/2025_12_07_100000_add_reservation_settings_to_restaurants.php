<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Reservation system type
            $table->enum('reservation_type', ['none', 'restaurante_famoso', 'external'])
                ->default('none')
                ->after('accepts_reservations');

            // External platform settings
            $table->string('reservation_platform')->nullable()->after('reservation_type'); // opentable, yelp, resy, other
            $table->string('reservation_external_url')->nullable()->after('reservation_platform');

            // Internal system settings (Restaurante Famoso)
            $table->json('reservation_settings')->nullable()->after('reservation_external_url');
            // Settings structure:
            // {
            //   "min_party_size": 1,
            //   "max_party_size": 20,
            //   "default_duration_minutes": 90,
            //   "advance_booking_days": 30,
            //   "same_day_cutoff_hours": 2,
            //   "time_slot_interval": 30,
            //   "require_confirmation": true,
            //   "auto_confirm": false,
            //   "confirmation_deadline_hours": 24,
            //   "no_show_policy": "none|deposit|cancel_fee",
            //   "deposit_amount": null
            // }

            // Capacity settings
            $table->json('reservation_hours')->nullable()->after('reservation_settings');
            // Structure:
            // {
            //   "monday": {"open": "11:00", "close": "22:00", "closed": false},
            //   "tuesday": {"open": "11:00", "close": "22:00", "closed": false},
            //   ...
            // }

            $table->integer('reservation_capacity_per_slot')->nullable()->after('reservation_hours');
            $table->integer('reservation_tables_count')->nullable()->after('reservation_capacity_per_slot');

            // Notification settings
            $table->string('reservation_notification_email')->nullable()->after('reservation_tables_count');
            $table->string('reservation_notification_phone')->nullable()->after('reservation_notification_email');
            $table->boolean('reservation_notify_whatsapp')->default(false)->after('reservation_notification_phone');
            $table->boolean('reservation_notify_sms')->default(false)->after('reservation_notify_whatsapp');
            $table->boolean('reservation_notify_email')->default(true)->after('reservation_notify_sms');

            // Customer notification settings
            $table->boolean('reservation_send_confirmation')->default(true)->after('reservation_notify_email');
            $table->boolean('reservation_send_reminder')->default(true)->after('reservation_send_confirmation');
            $table->integer('reservation_reminder_hours')->default(24)->after('reservation_send_reminder');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'reservation_type',
                'reservation_platform',
                'reservation_external_url',
                'reservation_settings',
                'reservation_hours',
                'reservation_capacity_per_slot',
                'reservation_tables_count',
                'reservation_notification_email',
                'reservation_notification_phone',
                'reservation_notify_whatsapp',
                'reservation_notify_sms',
                'reservation_notify_email',
                'reservation_send_confirmation',
                'reservation_send_reminder',
                'reservation_reminder_hours',
            ]);
        });
    }
};
