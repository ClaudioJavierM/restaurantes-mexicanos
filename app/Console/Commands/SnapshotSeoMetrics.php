<?php
namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SnapshotSeoMetrics extends Command
{
    protected $signature   = 'seo:daily-snapshot {--date=}';
    protected $description = 'Snapshot FAMER business metrics into seo_daily_metrics table';

    public function handle(): int
    {
        $date = $this->option('date') ?? now()->toDateString();

        $data = [
            'date'                    => $date,
            'new_claims'              => Restaurant::whereDate('claimed_at', $date)->count(),
            'total_claimed'           => Restaurant::where('is_claimed', true)->count(),
            'new_premium'             => Restaurant::whereDate('subscription_started_at', $date)
                                          ->whereIn('subscription_tier', ['premium','elite'])->count(),
            'total_premium'           => Restaurant::whereIn('subscription_tier', ['premium','elite'])
                                          ->where('subscription_status', 'active')->count(),
            'emails_sent'             => EmailLog::whereDate('sent_at', $date)->count(),
            'restaurants_with_email'  => Restaurant::where('status','approved')
                                          ->whereNotNull('email')->where('email','!=','')->count(),
            // GSC and Umami data will be filled by N8N workflow
            'gsc_clicks'             => 0,
            'gsc_impressions'        => 0,
            'total_sessions'         => 0,
        ];

        DB::table('seo_daily_metrics')->updateOrInsert(['date' => $date], $data);
        $this->info("SEO snapshot for {$date} saved.");
        return Command::SUCCESS;
    }
}
