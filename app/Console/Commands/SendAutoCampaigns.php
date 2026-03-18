<?php

namespace App\Console\Commands;

use App\Models\AutoCampaignConfig;
use App\Models\OwnerCampaign;
use App\Models\Restaurant;
use App\Models\RestaurantCustomer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAutoCampaigns extends Command
{
    protected $signature = 'famer:send-auto-campaigns {--type= : birthday|reactivation|welcome} {--dry-run : Preview without sending}';

    protected $description = 'Send automatic email campaigns (birthday, reactivation, welcome)';

    public function handle(): void
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');
        $types = $type ? [$type] : ['birthday', 'reactivation'];

        foreach ($types as $campaignType) {
            $this->processCampaignType($campaignType, $dryRun);
        }
    }

    protected function processCampaignType(string $type, bool $dryRun): void
    {
        $configs = AutoCampaignConfig::where('type', $type)
            ->where('is_active', true)
            ->with('restaurant')
            ->get();

        $this->info("Processing {$type} campaigns — {$configs->count()} active configs.");

        foreach ($configs as $config) {
            try {
                $recipients = $this->getRecipients($config);

                if ($recipients->isEmpty()) {
                    continue;
                }

                $this->info("  [{$config->restaurant->name}] {$recipients->count()} recipients");

                if ($dryRun) {
                    $this->line("  DRY RUN — would send to: " . $recipients->pluck('email')->join(', '));
                    continue;
                }

                foreach ($recipients as $customer) {
                    $this->sendEmail($config, $customer);
                }

                $config->update([
                    'last_run_at' => now(),
                    'total_sent'  => $config->total_sent + $recipients->count(),
                ]);

            } catch (\Exception $e) {
                Log::error("AutoCampaign error [{$type}] restaurant {$config->restaurant_id}: " . $e->getMessage());
                $this->error("  Error: " . $e->getMessage());
            }
        }
    }

    protected function getRecipients(AutoCampaignConfig $config): \Illuminate\Database\Eloquent\Collection
    {
        $query = RestaurantCustomer::where('restaurant_id', $config->restaurant_id)
            ->where('email_subscribed', true)
            ->whereNotNull('email');

        return match($config->type) {
            'birthday' => $query->whereNotNull('birthday')
                ->whereMonth('birthday', now()->month)
                ->whereDay('birthday', now()->day)
                ->get(),

            'reactivation' => $query->where(function ($q) {
                $q->whereNull('last_visit_at')
                  ->orWhere('last_visit_at', '<', now()->subDays(90));
            })->get(),

            'welcome' => $query->where('created_at', '>=', now()->subDay())
                ->where('visits_count', '<=', 1)
                ->get(),

            default => collect(),
        };
    }

    protected function sendEmail(AutoCampaignConfig $config, RestaurantCustomer $customer): void
    {
        $subject = $config->subject;
        $message = $config->message;
        $restaurantName = $config->restaurant->name;

        // Replace placeholders
        $message = str_replace(
            ['{nombre}', '{restaurante}', '{descuento}'],
            [$customer->name, $restaurantName, $config->coupon_discount_percent . '%'],
            $message
        );

        Mail::raw($message, function ($mail) use ($customer, $subject, $restaurantName, $config) {
            $mail->to($customer->email, $customer->name)
                 ->subject($subject)
                 ->from(
                     config('mail.from.address', 'noreply@restaurantesmexicanosfamosos.com'),
                     $restaurantName
                 );
        });

        Log::info("AutoCampaign sent [{$config->type}] to {$customer->email} for restaurant {$config->restaurant->name}");
    }
}
