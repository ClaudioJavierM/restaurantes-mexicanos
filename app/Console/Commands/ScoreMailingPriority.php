<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScoreMailingPriority extends Command
{
    protected $signature = 'famer:score-mailing-priority
                            {--country=US : Country code to filter (default: US)}
                            {--limit=3000 : Max restaurants to include in CSV export}
                            {--export : Export top restaurants to CSV file}';

    protected $description = 'Calculate mailing priority score for unclaimed restaurants and optionally export CSV';

    public function handle(): int
    {
        $country = $this->option('country');
        $limit   = (int) $this->option('limit');
        $export  = $this->option('export');

        $this->info("Calculating mailing priority scores for country={$country}...");

        // Fetch all target restaurants in chunks and update mailing_score
        $total = 0;

        Restaurant::query()
            ->where('country', $country)
            ->where('status', 'approved')
            ->where('is_claimed', false)
            ->select([
                'id',
                'email',
                'email_status',
                'claim_invitation_sent_at',
                'website',
                'phone',
            ])
            ->chunkById(500, function ($restaurants) use (&$total) {
                foreach ($restaurants as $r) {
                    $score = $this->calculateScore($r);

                    DB::table('restaurants')
                        ->where('id', $r->id)
                        ->update(['mailing_score' => $score]);

                    $total++;
                }
            });

        $this->info("Processed {$total} restaurants.");

        // Score distribution
        $this->newLine();
        $this->info('Score distribution:');

        $distribution = DB::table('restaurants')
            ->where('country', $country)
            ->where('status', 'approved')
            ->where('is_claimed', false)
            ->selectRaw("
                SUM(CASE WHEN mailing_score BETWEEN 0  AND 20 THEN 1 ELSE 0 END) as `0-20`,
                SUM(CASE WHEN mailing_score BETWEEN 21 AND 40 THEN 1 ELSE 0 END) as `21-40`,
                SUM(CASE WHEN mailing_score BETWEEN 41 AND 60 THEN 1 ELSE 0 END) as `41-60`,
                SUM(CASE WHEN mailing_score > 60              THEN 1 ELSE 0 END) as `60+`
            ")
            ->first();

        $this->table(
            ['0-20', '21-40', '41-60', '60+'],
            [[(int)$distribution->{'0-20'}, (int)$distribution->{'21-40'}, (int)$distribution->{'41-60'}, (int)$distribution->{'60+'}]]
        );

        // Top 10 states by average score
        $this->newLine();
        $this->info('Top 10 states by average mailing score:');

        $topStates = DB::table('restaurants')
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.country', $country)
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_claimed', false)
            ->selectRaw('states.name as state_name, ROUND(AVG(restaurants.mailing_score), 2) as avg_score, COUNT(*) as total')
            ->groupBy('states.id', 'states.name')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        $this->table(
            ['State', 'Avg Score', 'Restaurants'],
            $topStates->map(fn($s) => [$s->state_name, $s->avg_score, $s->total])->toArray()
        );

        // Export CSV if requested
        if ($export) {
            $this->exportCsv($country, $limit);
        }

        return self::SUCCESS;
    }

    /**
     * Calculate the mailing priority score for a restaurant.
     * Uses only verified columns: email, email_status, claim_invitation_sent_at, website, phone.
     */
    protected function calculateScore(object $r): int
    {
        $score = 0;

        // status = 'approved': base +5 (already filtered by query, always true here)
        $score += 5;

        // No email at all — postal is the only contact channel
        if (is_null($r->email)) {
            $score += 25;
        } else {
            // Has email
            $emailStatus = $r->email_status;

            if (in_array($emailStatus, ['no_mx', 'bounced', 'invalid_syntax'], true)) {
                // Email failed — postal is the fallback
                $score += 20;
            } elseif ($emailStatus === 'valid' && !is_null($r->claim_invitation_sent_at)) {
                // Valid email, already contacted, no response
                $score += 15;
            } elseif ($emailStatus === 'valid') {
                // Valid email, not yet contacted
                $score += 10;
            } elseif (is_null($emailStatus)) {
                // Has email but never verified
                $score += 5;
            }
        }

        // Has a website — more established business
        if (!is_null($r->website)) {
            $score += 15;
        }

        // Has phone
        if (!is_null($r->phone)) {
            $score += 10;
        }

        return $score;
    }

    /**
     * Export top restaurants to storage/app/mailing/priority_{date}.csv
     */
    protected function exportCsv(string $country, int $limit): void
    {
        $date     = now()->format('Y-m-d');
        $filename = "mailing/priority_{$date}.csv";

        Storage::makeDirectory('mailing');

        $restaurants = DB::table('restaurants')
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.country', $country)
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_claimed', false)
            ->select([
                'restaurants.id',
                'restaurants.name',
                'restaurants.address',
                'restaurants.city',
                'states.name as state_name',
                'restaurants.zip_code',
                'restaurants.phone',
                'restaurants.website',
                'restaurants.email',
                'restaurants.mailing_score',
            ])
            ->orderByDesc('restaurants.mailing_score')
            ->limit($limit)
            ->get();

        // Build CSV content
        $header = ['id', 'name', 'address', 'city', 'state_name', 'zip', 'phone', 'website', 'email', 'mailing_score'];
        $lines  = [implode(',', $header)];

        foreach ($restaurants as $r) {
            $lines[] = implode(',', [
                $r->id,
                $this->csvEscape($r->name),
                $this->csvEscape($r->address),
                $this->csvEscape($r->city),
                $this->csvEscape($r->state_name),
                $this->csvEscape($r->zip_code),
                $this->csvEscape($r->phone),
                $this->csvEscape($r->website),
                $this->csvEscape($r->email),
                $r->mailing_score,
            ]);
        }

        Storage::put($filename, implode("\n", $lines));

        $path = storage_path("app/{$filename}");
        $this->newLine();
        $this->info("CSV exported: {$path}");
        $this->info("Rows: " . count($restaurants));
    }

    protected function csvEscape(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }
        // Wrap in quotes if the value contains a comma, quote, or newline
        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
}
