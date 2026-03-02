<?php

namespace App\Console\Commands;

use App\Services\StripeService;
use Illuminate\Console\Command;

class CreateStripeCoupon extends Command
{
    protected $signature = 'stripe:coupon
                            {code : The promotion code (e.g., LAUNCH50)}
                            {--percent= : Percentage discount (e.g., 50 for 50%)}
                            {--amount= : Fixed amount discount in dollars (e.g., 10 for $10)}
                            {--duration=once : Duration: once, repeating, or forever}
                            {--months= : Number of months if duration is repeating}
                            {--max= : Maximum number of redemptions}
                            {--expires= : Expiration date (Y-m-d format)}';

    protected $description = 'Create a Stripe coupon and promotion code';

    public function handle()
    {
        $stripeService = new StripeService();

        $code = strtoupper($this->argument('code'));
        $percentOff = $this->option('percent');
        $amountOff = $this->option('amount');
        $duration = $this->option('duration');
        $durationMonths = $this->option('months');
        $maxRedemptions = $this->option('max');
        $expiresAt = $this->option('expires');

        // Validate input
        if (!$percentOff && !$amountOff) {
            $this->error('You must specify either --percent or --amount');
            return 1;
        }

        if ($percentOff && $amountOff) {
            $this->error('You cannot specify both --percent and --amount');
            return 1;
        }

        try {
            // Create coupon data
            $couponData = [
                'duration' => $duration,
                'name' => $code,
            ];

            if ($percentOff) {
                $couponData['percent_off'] = (float) $percentOff;
            }

            if ($amountOff) {
                $couponData['amount_off'] = (float) $amountOff * 100; // Convert to cents
                $couponData['currency'] = 'usd';
            }

            if ($durationMonths && $duration === 'repeating') {
                $couponData['duration_in_months'] = (int) $durationMonths;
            }

            if ($maxRedemptions) {
                $couponData['max_redemptions'] = (int) $maxRedemptions;
            }

            // Create coupon
            $this->info('Creating coupon in Stripe...');
            $coupon = $stripeService->createCoupon($couponData);
            $this->info("✓ Coupon created: {$coupon->id}");

            // Create promotion code
            $this->info('Creating promotion code...');
            $promotionOptions = [];

            if ($maxRedemptions) {
                $promotionOptions['max_redemptions'] = (int) $maxRedemptions;
            }

            if ($expiresAt) {
                $expiresTimestamp = strtotime($expiresAt . ' 23:59:59');
                $promotionOptions['expires_at'] = $expiresTimestamp;
            }

            $promotionCode = $stripeService->createPromotionCode($coupon->id, $code, $promotionOptions);

            $this->info("✓ Promotion code created: {$promotionCode->code}");
            $this->line('');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("  Promotion Code: <fg=green;options=bold>{$promotionCode->code}</>");

            if ($percentOff) {
                $this->line("  Discount: <fg=yellow>{$percentOff}% off</>");
            } else {
                $this->line("  Discount: <fg=yellow>\${$amountOff} off</>");
            }

            $this->line("  Duration: <fg=cyan>{$duration}</>");

            if ($maxRedemptions) {
                $this->line("  Max Uses: <fg=magenta>{$maxRedemptions}</>");
            }

            if ($expiresAt) {
                $this->line("  Expires: <fg=red>{$expiresAt}</>");
            }

            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line('');
            $this->info('✓ Coupon ready to use!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating coupon: ' . $e->getMessage());
            return 1;
        }
    }
}
