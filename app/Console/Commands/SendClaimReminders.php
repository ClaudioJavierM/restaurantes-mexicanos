<?php

namespace App\Console\Commands;

use App\Models\ClaimVerification;
use App\Mail\ClaimVerificationReminder;
use App\Mail\ClaimApprovalPending;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendClaimReminders extends Command
{
    protected $signature = 'claims:send-reminders';
    protected $description = 'Send reminder emails for incomplete claim verifications';

    public function handle()
    {
        $this->info('Sending claim reminders...');
        
        // 1. Claims pending verification (started but not verified)
        // First reminder: 1 hour after creation
        // Second reminder: 24 hours after creation
        $this->sendPendingVerificationReminders();
        
        // 2. Claims verified but not approved
        // First reminder: 24 hours after verification
        // Second reminder: 3 days after verification
        $this->sendPendingApprovalReminders();
        
        $this->info('Done!');
        return Command::SUCCESS;
    }
    
    protected function sendPendingVerificationReminders()
    {
        // First reminder: 1 hour after creation, not yet reminded
        $firstReminder = ClaimVerification::where('status', 'pending')
            ->where('is_verified', false)
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', Carbon::now()->subHour())
            ->where('created_at', '>=', Carbon::now()->subHours(25)) // Don't send to very old ones
            ->get();
            
        foreach ($firstReminder as $claim) {
            try {
                Mail::to($claim->owner_email)->send(new ClaimVerificationReminder($claim, 1));
                $claim->update(['reminder_sent_at' => now(), 'reminder_count' => 1]);
                $this->info("First reminder sent to: {$claim->owner_email}");
            } catch (\Exception $e) {
                $this->error("Failed to send to {$claim->owner_email}: " . $e->getMessage());
            }
        }
        
        // Second reminder: 24 hours after creation, already reminded once
        $secondReminder = ClaimVerification::where('status', 'pending')
            ->where('is_verified', false)
            ->where('reminder_count', 1)
            ->where('reminder_sent_at', '<=', Carbon::now()->subHours(23))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();
            
        foreach ($secondReminder as $claim) {
            try {
                Mail::to($claim->owner_email)->send(new ClaimVerificationReminder($claim, 2));
                $claim->update(['reminder_sent_at' => now(), 'reminder_count' => 2]);
                $this->info("Second reminder sent to: {$claim->owner_email}");
            } catch (\Exception $e) {
                $this->error("Failed to send to {$claim->owner_email}: " . $e->getMessage());
            }
        }
        
        $this->info("Pending verification reminders: " . ($firstReminder->count() + $secondReminder->count()));
    }
    
    protected function sendPendingApprovalReminders()
    {
        // First reminder: 24 hours after verification
        $firstReminder = ClaimVerification::where('status', 'verified')
            ->where('is_verified', true)
            ->whereNull('approval_reminder_sent_at')
            ->where('verified_at', '<=', Carbon::now()->subDay())
            ->where('verified_at', '>=', Carbon::now()->subDays(7))
            ->get();
            
        foreach ($firstReminder as $claim) {
            try {
                Mail::to($claim->owner_email)->send(new ClaimApprovalPending($claim, 1));
                $claim->update(['approval_reminder_sent_at' => now(), 'approval_reminder_count' => 1]);
                $this->info("Approval reminder sent to: {$claim->owner_email}");
            } catch (\Exception $e) {
                $this->error("Failed to send to {$claim->owner_email}: " . $e->getMessage());
            }
        }
        
        // Second reminder: 3 days after verification
        $secondReminder = ClaimVerification::where('status', 'verified')
            ->where('is_verified', true)
            ->where('approval_reminder_count', 1)
            ->where('approval_reminder_sent_at', '<=', Carbon::now()->subDays(2))
            ->where('verified_at', '>=', Carbon::now()->subDays(14))
            ->get();
            
        foreach ($secondReminder as $claim) {
            try {
                Mail::to($claim->owner_email)->send(new ClaimApprovalPending($claim, 2));
                $claim->update(['approval_reminder_sent_at' => now(), 'approval_reminder_count' => 2]);
                $this->info("Second approval reminder sent to: {$claim->owner_email}");
            } catch (\Exception $e) {
                $this->error("Failed to send to {$claim->owner_email}: " . $e->getMessage());
            }
        }
        
        $this->info("Pending approval reminders: " . ($firstReminder->count() + $secondReminder->count()));
    }
}
