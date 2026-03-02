<?php
/**
 * Script to create example Stripe Promotion Codes for testing
 * Run this to create test coupons
 *
 * Usage: php create-stripe-coupons.php
 */

require __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Coupon;
use Stripe\PromotionCode;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set Stripe API key
Stripe::setApiKey($_ENV['STRIPE_SECRET']);

echo "🎟️  Creating Stripe Coupons and Promotion Codes...\n\n";

// Define coupons
$coupons = [
    [
        'code' => 'LAUNCH50',
        'name' => 'Launch Discount - 50% Off First Month',
        'percent_off' => 50,
        'duration' => 'once', // once, forever, repeating
        'max_redemptions' => 100,
    ],
    [
        'code' => 'WELCOME25',
        'name' => 'Welcome Discount - 25% Off First Month',
        'percent_off' => 25,
        'duration' => 'once',
        'max_redemptions' => 500,
    ],
    [
        'code' => 'PREMIUM3MONTHS',
        'name' => 'Premium Trial - 20% Off for 3 Months',
        'percent_off' => 20,
        'duration' => 'repeating',
        'duration_in_months' => 3,
        'max_redemptions' => 50,
    ],
];

$createdCodes = [];

foreach ($coupons as $couponData) {
    echo "Creating coupon: {$couponData['code']}...\n";

    try {
        // Create coupon
        $couponParams = [
            'name' => $couponData['name'],
            'percent_off' => $couponData['percent_off'],
            'duration' => $couponData['duration'],
            'max_redemptions' => $couponData['max_redemptions'] ?? null,
        ];

        if ($couponData['duration'] === 'repeating') {
            $couponParams['duration_in_months'] = $couponData['duration_in_months'];
        }

        $coupon = Coupon::create($couponParams);

        echo "✅ Coupon created: {$coupon->id} ({$coupon->percent_off}% off)\n";

        // Create promotion code
        $promotionCode = PromotionCode::create([
            'coupon' => $coupon->id,
            'code' => $couponData['code'],
            'active' => true,
            'max_redemptions' => $couponData['max_redemptions'] ?? null,
        ]);

        echo "✅ Promotion code created: {$promotionCode->code}\n";

        $createdCodes[] = [
            'code' => $promotionCode->code,
            'discount' => "{$coupon->percent_off}% off",
            'duration' => $coupon->duration,
        ];

        echo "\n";

    } catch (Exception $e) {
        echo "❌ Error creating {$couponData['code']}: " . $e->getMessage() . "\n\n";
    }
}

// Display results
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ COUPONS CREATED!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📝 Test these promotion codes in your checkout:\n\n";

foreach ($createdCodes as $code) {
    echo "  • {$code['code']} - {$code['discount']} ({$code['duration']})\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔗 View coupons in Stripe Dashboard:\n";
echo "https://dashboard.stripe.com/coupons\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 Test the checkout flow:\n";
echo "1. Go to: https://www.restaurantesmexicanosfamosos.com/claim\n";
echo "2. Search for a restaurant and claim it\n";
echo "3. Enter one of the codes above in the payment step\n";
echo "4. Complete the checkout process\n\n";
