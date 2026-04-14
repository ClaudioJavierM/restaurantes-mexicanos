<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;

class ApprovePendingRestaurants extends Command
{
    protected $signature = 'famer:approve-pending
                            {--dry-run : Show count without making changes}
                            {--limit=500 : Maximum number of restaurants to approve per run}';

    protected $description = 'Auto-approve restaurants with complete data (name + address + city)';

    public function handle(): int
    {
        try {
            $limit = (int) $this->option('limit');
            $dryRun = $this->option('dry-run');

            $query = Restaurant::where('status', 'pending')
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->whereNotNull('city')
                ->where('city', '!=', '');

            $total = $query->count();

            if ($dryRun) {
                $toProcess = min($total, $limit);
                $this->info("Would approve {$toProcess} restaurants (dry-run). Total eligible: {$total}.");
                return 0;
            }

            $approved = 0;

            $query->limit($limit)->chunkById(100, function ($restaurants) use (&$approved) {
                foreach ($restaurants as $restaurant) {
                    $restaurant->update(['status' => 'approved']);
                    $approved++;
                }
            });

            $this->info("Approved {$approved} restaurants.");

            if ($total > $limit) {
                $this->warn("Note: {$total} total eligible, only {$limit} processed this run. Run again to approve more.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error approving restaurants: " . $e->getMessage());
            \Log::error('famer:approve-pending failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}
