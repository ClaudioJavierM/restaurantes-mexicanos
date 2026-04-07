<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('preview_text')->nullable(); // Email preview text
            $table->enum('type', ['invitation', 'follow_up', 'stats', 'urgency', 'welcome', 'upgrade', 'newsletter', 'promo', 'famer_report'])->default('newsletter');
            $table->text('content'); // HTML template content
            $table->json('variables')->nullable(); // Available merge tags
            $table->json('audience_filter')->nullable(); // Filter criteria for recipients
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('scheduled_at');
        });

        // Add campaign_id to email_logs if not exists
        if (!Schema::hasColumn('email_logs', 'campaign_id')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->nullOnDelete();
                $table->string('tracking_token', 64)->nullable()->unique();
                $table->integer('open_count')->default(0);
                $table->integer('click_count')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('email_logs', 'campaign_id')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->dropForeign(['campaign_id']);
                $table->dropColumn(['campaign_id', 'tracking_token', 'open_count', 'click_count']);
            });
        }
        Schema::dropIfExists('email_campaigns');
    }
};
