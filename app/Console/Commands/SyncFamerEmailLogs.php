<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncFamerEmailLogs extends Command
{
    protected $signature = "famer:sync-logs";
    protected $description = "Sync FAMER email records to email_logs table";

    public function handle(): int
    {
        $this->info("Sincronizando emails FAMER a email_logs...");
        
        // Get restaurants with famer_email_1_sent_at that are not in email_logs
        $restaurants = DB::table("restaurants")
            ->whereNotNull("famer_email_1_sent_at")
            ->whereNotNull("email")
            ->where("email", "<>", "")
            ->get();
        
        $synced = 0;
        $skipped = 0;
        
        foreach ($restaurants as $r) {
            // Check if already exists
            $exists = DB::table("email_logs")
                ->where("category", "famer_email_1")
                ->where("restaurant_id", $r->id)
                ->exists();
            
            if (!$exists) {
                DB::table("email_logs")->insert([
                    "type" => "campaign",
                    "category" => "famer_email_1",
                    "to_email" => $r->email,
                    "to_name" => $r->name,
                    "from_email" => "noreply@restaurantesmexicanosfamosos.com",
                    "from_name" => "FAMER - Restaurantes Mexicanos",
                    "subject" => "Tu restaurante ya esta en FAMER",
                    "status" => "sent",
                    "sent_at" => $r->famer_email_1_sent_at,
                    "restaurant_id" => $r->id,
                    "created_at" => $r->famer_email_1_sent_at,
                    "updated_at" => now(),
                ]);
                $synced++;
            } else {
                $skipped++;
            }
        }
        
        $this->info("Sincronizados: {$synced}");
        $this->info("Ya existían: {$skipped}");
        
        return 0;
    }
}
