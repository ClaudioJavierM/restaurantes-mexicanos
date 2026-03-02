<?php

namespace App\Console\Commands;

use App\Services\ApiUsageTracker;
use Illuminate\Console\Command;

class CheckApiUsage extends Command
{
    protected $signature = 'api:check-usage';
    protected $description = 'Check Google Places API usage and costs';

    public function handle()
    {
        $stats = ApiUsageTracker::getStats();

        $this->info('===========================================');
        $this->info('   Google Places API Usage Report');
        $this->info('===========================================');
        $this->newLine();

        // Today's usage
        $this->line('<fg=cyan>📅 TODAY</>');
        $this->line("   Requests: <fg=yellow>{$stats['today']['requests']}</>");
        $this->line("   Cost: $<fg=yellow>" . number_format($stats['today']['cost'], 2) . "</>");
        $this->newLine();

        // This month's usage
        $this->line('<fg=cyan>📊 THIS MONTH</>');
        $this->line("   Total Cost: $<fg=yellow>" . number_format($stats['this_month']['cost'], 2) . "</>");
        $this->line("   Budget: $<fg=green>" . number_format($stats['this_month']['budget'], 2) . "</>");
        $this->line("   Remaining: $<fg=green>" . number_format($stats['this_month']['remaining'], 2) . "</>");
        
        $percentage = round($stats['this_month']['percentage_used'], 1);
        $color = $percentage < 50 ? 'green' : ($percentage < 75 ? 'yellow' : 'red');
        $this->line("   Used: <fg={$color}>{$percentage}%</>");
        
        // Progress bar
        $this->newLine();
        $barLength = 40;
        $filled = (int) ($barLength * ($percentage / 100));
        $bar = str_repeat('█', $filled) . str_repeat('░', $barLength - $filled);
        $this->line("   [{$bar}] {$percentage}%");
        $this->newLine();

        // Limits
        $this->line('<fg=cyan>⚙️  LIMITS</>');
        $this->line("   Daily Requests: <fg=yellow>{$stats['limits']['daily_requests']}</>");
        $this->line("   Monthly Budget: $<fg=yellow>" . number_format($stats['limits']['monthly_budget'], 2) . "</>");
        $this->line("   Alert Threshold: $<fg=yellow>" . number_format($stats['limits']['alert_threshold'], 2) . "</>");
        $this->newLine();

        // Warnings
        if ($stats['this_month']['cost'] >= $stats['limits']['monthly_budget']) {
            $this->error('⚠️  BUDGET EXCEEDED! API calls will be blocked.');
        } elseif ($stats['this_month']['cost'] >= $stats['limits']['alert_threshold']) {
            $this->warn('⚠️  Approaching budget limit. Use API sparingly.');
        } else {
            $this->info('✅ Usage is within safe limits.');
        }

        $this->newLine();
        $this->info('===========================================');

        return 0;
    }
}
