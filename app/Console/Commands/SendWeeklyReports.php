<?php

namespace App\Console\Commands;

use App\Mail\WeeklyReport;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReports extends Command
{
    protected $signature = 'reports:send-weekly {--test= : Send test email to specific restaurant ID}';
    protected $description = 'Send weekly performance reports to restaurant owners';

    public function handle()
    {
        $testId = $this->option('test');
        
        if ($testId) {
            $restaurant = Restaurant::find($testId);
            if (!$restaurant) {
                $this->error('Restaurant not found');
                return 1;
            }
            
            $this->sendReport($restaurant);
            $this->info("Test report sent for: {$restaurant->name}");
            return 0;
        }
        
        $restaurants = Restaurant::where('is_claimed', true)
            ->whereHas('owner')
            ->with('owner')
            ->get();
        
        $count = 0;
        $errors = 0;
        
        $this->info("Sending weekly reports to {$restaurants->count()} restaurants...");
        
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();
        
        foreach ($restaurants as $restaurant) {
            try {
                $this->sendReport($restaurant);
                $count++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error sending to {$restaurant->name}: " . $e->getMessage());
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Weekly reports sent successfully: {$count}");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }
        
        return 0;
    }
    
    protected function sendReport(Restaurant $restaurant): void
    {
        $owner = $restaurant->owner;
        
        if (!$owner || !$owner->email) {
            throw new \Exception('No owner email found');
        }
        
        Mail::to($owner->email)->send(new WeeklyReport($restaurant));
    }
}
