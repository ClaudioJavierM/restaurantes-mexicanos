<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;

class FixDescriptionReviewCounts extends Command
{
    protected $signature = 'restaurants:fix-descriptions 
                            {--dry-run : Show what would be changed without updating}
                            {--limit=0 : Limit number of restaurants to process}';

    protected $description = 'Fix restaurant descriptions that have outdated review counts by making them generic';

    public function handle(): int
    {
        $this->info('🔧 Fixing restaurant descriptions with outdated review counts...');
        $this->newLine();

        $query = Restaurant::where('status', 'approved')
            ->where('description', 'like', '%based on%reviews%');

        if ($limit = $this->option('limit')) {
            $query->limit($limit);
        }

        $restaurants = $query->get();
        $this->info("Found {$restaurants->count()} restaurants with review counts in descriptions");

        $isDryRun = $this->option('dry-run');
        if ($isDryRun) {
            $this->warn('DRY RUN - No changes will be made');
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $updated = 0;
        $examples = [];

        foreach ($restaurants as $restaurant) {
            $original = $restaurant->description;
            
            // Pattern to match: "With a X.X star rating based on XX reviews, it's a popular choice"
            // Replace with: "Highly rated by customers, it's a popular choice"
            $newDescription = preg_replace(
                '/With a [\d.]+ star rating based on \d+ reviews, it\'s a popular choice/',
                'Highly rated by customers, it\'s a popular choice',
                $original
            );

            // Also handle other patterns
            $newDescription = preg_replace(
                '/With a [\d.]+ star rating based on \d+ reviews\./',
                'Highly rated by customers.',
                $newDescription
            );

            if ($newDescription !== $original) {
                if (!$isDryRun) {
                    $restaurant->description = $newDescription;
                    $restaurant->save();
                }
                $updated++;
                
                if (count($examples) < 3) {
                    $examples[] = [
                        'name' => $restaurant->name,
                        'before' => substr($original, 0, 100) . '...',
                        'after' => substr($newDescription, 0, 100) . '...',
                    ];
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Updated: {$updated} descriptions");

        if (count($examples) > 0) {
            $this->newLine();
            $this->info('Examples:');
            foreach ($examples as $ex) {
                $this->line("Restaurant: {$ex['name']}");
                $this->line("  Before: {$ex['before']}");
                $this->line("  After:  {$ex['after']}");
                $this->newLine();
            }
        }

        return Command::SUCCESS;
    }
}
