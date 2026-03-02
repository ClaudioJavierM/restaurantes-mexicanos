<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PDO;

class SendWelcomeEmails extends Command
{
    protected $signature = "restaurants:send-welcome-emails
                            {--limit=50 : Number of restaurants to process per run}
                            {--dry-run : Show what would be sent without actually sending}
                            {--state= : Filter by state code}
                            {--country=US : Filter by country (US or MX)}";

    protected $description = "Add restaurants to Listmonk welcome list and send batch campaign";

    private $templateId = 11;
    private $listId = 9;
    private $pdo;

    public function handle()
    {
        $limit = (int) $this->option("limit");
        $dryRun = $this->option("dry-run");
        $state = $this->option("state");
        $country = $this->option("country");

        $this->info("=== Welcome Emails - Batch Mode ===");
        $this->info("Limit: {$limit} | Dry Run: " . ($dryRun ? "Yes" : "No"));

        // Connect to Listmonk DB
        try {
            $this->pdo = new PDO(
                "pgsql:host=localhost;port=5433;dbname=listmonk",
                "listmonk",
                "listmonk_s3cur3_2024"
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            $this->error("Cannot connect to Listmonk DB: " . $e->getMessage());
            return 1;
        }

        // Get restaurants
        $query = Restaurant::whereNull("welcome_email_sent_at")
            ->whereNull("claimed_at")
            ->where("country", $country)
            ->whereNotNull("yelp_id")
            ->whereNotNull("email")
            ->where("email", "NOT LIKE", "%placeholder%");

        if ($state) {
            $query->whereHas("state", fn($q) => $q->where("code", $state));
        }

        $restaurants = $query->limit($limit)->get();
        $this->info("Found {$restaurants->count()} restaurants to process");

        if ($restaurants->isEmpty()) {
            $this->info("No restaurants pending welcome email.");
            return 0;
        }

        // Step 1: Add all subscribers
        $this->info("\n[1/2] Adding subscribers to Listmonk...");
        $added = 0;
        $bar = $this->output->createProgressBar($restaurants->count());

        foreach ($restaurants as $restaurant) {
            $bar->advance();

            if ($dryRun) continue;

            $email = $restaurant->email ?: "owner-{$restaurant->id}@placeholder.famexrest.com";
            $attribs = [
                "restaurant_id" => $restaurant->id,
                "restaurant_name" => $restaurant->name,
                "city" => $restaurant->city,
                "state" => $restaurant->state->name ?? "",
                "listing_url" => "https://famousmexicanrestaurants.com/restaurant/" . $restaurant->slug,
                "claim_url" => "https://famousmexicanrestaurants.com/claim/{$restaurant->id}",
                "yelp_rating" => $restaurant->yelp_rating ?? "N/A",
                "google_rating" => $restaurant->google_rating ?? "N/A",
                "estimated_views" => $this->estimateViews($restaurant),
                "estimated_ad_value" => "$" . number_format($this->estimateViews($restaurant) * 1.25, 2),
            ];

            try {
                $subscriberId = $this->createSubscriber($email, $restaurant->name, $attribs);
                $this->addToList($subscriberId);

                // Mark in Laravel DB
                $restaurant->update([
                    "welcome_email_sent_at" => now(),
                    
                ]);

                $added++;
            } catch (\Exception $e) {
                Log::error("Failed to add subscriber", [
                    "restaurant_id" => $restaurant->id,
                    "error" => $e->getMessage()
                ]);
            }
        }
        $bar->finish();

        if ($dryRun) {
            $this->newLine(2);
            $this->info("[DRY RUN] Would add {$restaurants->count()} subscribers");
            return 0;
        }

        // Step 2: Create and start batch campaign
        $this->newLine(2);
        $this->info("[2/2] Creating batch campaign...");

        try {
            $campaignId = $this->createBatchCampaign($added, $country);
            $this->info("Campaign ID: {$campaignId} created and started");
            $this->info("Listmonk will send emails respecting rate limits.");
        } catch (\Exception $e) {
            $this->error("Failed to create campaign: " . $e->getMessage());
        }

        $this->newLine();
        $this->info("=== Complete ===");
        $this->info("Subscribers added: {$added}");

        return 0;
    }

    private function createSubscriber(string $email, string $name, array $attribs): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO subscribers (uuid, email, name, status, attribs, created_at, updated_at)
            VALUES (gen_random_uuid(), :email, :name, 'enabled', :attribs::jsonb, NOW(), NOW())
            ON CONFLICT (email) DO UPDATE SET attribs = EXCLUDED.attribs, updated_at = NOW()
            RETURNING id
        ");
        $stmt->execute([
            ":email" => $email,
            ":name" => $name,
            ":attribs" => json_encode($attribs),
        ]);
        return (int) $stmt->fetchColumn();
    }

    private function addToList(int $subscriberId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO subscriber_lists (subscriber_id, list_id, status, created_at, updated_at)
            VALUES (:sid, :lid, 'confirmed', NOW(), NOW())
            ON CONFLICT (subscriber_id, list_id) DO UPDATE SET status = 'confirmed'
        ");
        $stmt->execute([":sid" => $subscriberId, ":lid" => $this->listId]);
    }

    private function createBatchCampaign(int $count, string $country): int
    {
        $date = now()->format("Y-m-d H:i");
        $name = "Welcome Batch - {$country} - {$date} ({$count} restaurants)";
        $subject = "{{ .Subscriber.Attribs.restaurant_name }} - Tu restaurante ahora esta en nuestra plataforma";

        // Get template body
        $stmt = $this->pdo->prepare("SELECT body FROM templates WHERE id = :id");
        $stmt->execute([":id" => $this->templateId]);
        $body = $stmt->fetchColumn();

        // Create campaign
        $stmt = $this->pdo->prepare("
            INSERT INTO campaigns (uuid, name, subject, from_email, body, content_type, send_at, status, type, template_id, messenger, created_at, updated_at)
            VALUES (gen_random_uuid(), :name, :subject, :from, :body, :content_type, NOW(), :status, :type, :tid, :messenger, NOW(), NOW())
            RETURNING id
        ");
        $stmt->execute([
            ":name" => $name,
            ":subject" => $subject,
            ":from" => "Restaurantes Mexicanos Famosos <noreply@restaurantesmexicanosfamosos.com>",
            ":body" => $body,
            ":tid" => $this->templateId,
            ":content_type" => "richtext",
            ":status" => "draft",
            ":type" => "regular",
            ":messenger" => "email",
        ]);
        $campaignId = (int) $stmt->fetchColumn();

        // Link to list
        $stmt = $this->pdo->prepare("INSERT INTO campaign_lists (campaign_id, list_id) VALUES (:cid, :lid)");
        $stmt->execute([":cid" => $campaignId, ":lid" => $this->listId]);

        // Start campaign
        $stmt = $this->pdo->prepare("UPDATE campaigns SET status = 'running', started_at = NOW() WHERE id = :id");
        $stmt->execute([":id" => $campaignId]);

        return $campaignId;
    }

    private function estimateViews(Restaurant $restaurant): int
    {
        $base = 50;
        if ($restaurant->yelp_rating >= 4.5) $base += 100;
        elseif ($restaurant->yelp_rating >= 4.0) $base += 50;
        if ($restaurant->yelp_reviews_count > 100) $base += 50;
        return $base;
    }
}
