<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;

class ExportToMautic extends Command
{
    protected $signature = "restaurants:export-mautic {--output=/tmp/restaurants_mautic.csv}";
    protected $description = "Export restaurants to CSV for Mautic import";

    public function handle()
    {
        $output = $this->option("output");
        $fp = fopen($output, "w");
        
        fputcsv($fp, ["email", "firstname", "company", "phone", "city", "state", "tags", "custom_restaurant_id"]);
        
        $count = 0;
        Restaurant::with("state")
            ->where("status", "approved")
            ->chunk(500, function($restaurants) use ($fp, &$count) {
                foreach($restaurants as $r) {
                    $email = $r->owner_email ?: $r->email;
                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
                    
                    $tags = ["restaurant"];
                    if ($r->claim_invitation_sent_at) $tags[] = "invitation_sent";
                    if ($r->state) $tags[] = "state_" . strtolower($r->state->code ?? "");
                    
                    fputcsv($fp, [
                        $email,
                        $r->name ?? "",
                        $r->name ?? "",
                        $r->phone ?? "",
                        $r->city ?? "",
                        $r->state->code ?? "",
                        implode("|", $tags),
                        $r->id
                    ]);
                    $count++;
                }
            });
        
        fclose($fp);
        $this->info("Exportados: {$count} restaurantes a {$output}");
        
        return 0;
    }
}
