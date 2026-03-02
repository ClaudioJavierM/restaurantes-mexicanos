<?php

namespace App\Console\Commands;

use App\Models\ImportStat;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillImportStats extends Command
{
    protected $signature = "imports:backfill-stats {--days=90 : Number of days to backfill}";
    protected $description = "Backfill import statistics from existing restaurant data";

    public function handle(): int
    {
        $days = (int) $this->option("days");
        $this->info("Backfilling import statistics for the last {$days} days...");

        $stats = Restaurant::selectRaw("DATE(created_at) as stat_date, COALESCE(import_source, \"manual\") as source, COUNT(*) as imported")
            ->where("created_at", ">=", now()->subDays($days))
            ->groupBy("stat_date", "source")
            ->orderBy("stat_date")
            ->get();

        $bar = $this->output->createProgressBar($stats->count());
        $bar->start();

        $created = 0;
        foreach ($stats as $stat) {
            ImportStat::updateOrCreate(
                [
                    "stat_date" => $stat->stat_date,
                    "source" => $stat->source,
                ],
                [
                    "imported" => $stat->imported,
                    "total_found" => $stat->imported,
                ]
            );
            $created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Created/updated {$created} import stat records.");

        return Command::SUCCESS;
    }
}
