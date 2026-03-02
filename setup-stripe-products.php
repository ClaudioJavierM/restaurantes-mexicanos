<?php
/**
 * Script to create Stripe Products and Prices for Restaurant Subscriptions
 * Run this once to set up your Stripe account with the necessary products
 *
 * Usage: php setup-stripe-products.php
 */

require __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set Stripe API key
Stripe::setApiKey($_ENV['STRIPE_SECRET']);

echo "🚀 Setting up Stripe Products and Prices...\n\n";

// Define products
$products = [
    'claimed' => [
        'name' => 'Claimed Plan',
        'description' => 'Verified profile with basic features and 1 annual coupon (5% off)',
        'price' => 9.99,
        'features' => [
            'Verified profile with badge',
            'Edit basic information',
            'Respond to reviews',
            'Basic analytics',
            'Up to 10 photos',
            '1 Annual Coupon (5% off) - Save up to $300/year on MF Imports, Decorarmex, Mexartcraft & Refrimex',
        ],
    ],
    'premium' => [
        'name' => 'Premium Plan',
        'description' => 'Featured listing with advanced features and 4 quarterly coupons (10% off)',
        'price' => 39.00,
        'features' => [
            'Everything in Claimed +',
            'Featured Badge',
            'Top 3 in searches',
            'Digital Menu + QR Code',
            'Online Orders Widget',
            'Reservation System',
            'Bilingual AI Chatbot',
            '+ 12 more benefits',
            '4 Quarterly Coupons (10% off) - Save up to $500 per coupon ($2,000/year total)',
        ],
    ],
    'elite' => [
        'name' => 'Elite Plan',
        'description' => 'Premium positioning with all features and elite savings package',
        'price' => 99.00,
        'features' => [
            'Everything in Premium +',
            '#1 Position Guaranteed',
            'White Label Mobile App',
            'Complete Website',
            'Professional Photography',
            'Account Manager',
            'POS Integration',
            '+ 10 more benefits',
            'Elite Savings: 6 coupons (15% off, $750 each) OR 1 project coupon (15%, $1,500 cap) + 4 quarterly (10%, $500 each)',
        ],
    ],
];

$priceIds = [];

foreach ($products as $key => $productData) {
    echo "Creating product: {$productData['name']}...\n";

    try {
        // Create product
        $product = Product::create([
            'name' => $productData['name'],
            'description' => $productData['description'],
            'metadata' => [
                'plan_key' => $key,
                'features' => json_encode($productData['features']),
            ],
        ]);

        echo "✅ Product created: {$product->id}\n";

        // Create recurring price
        $price = Price::create([
            'product' => $product->id,
            'unit_amount' => (int)($productData['price'] * 100), // Convert to cents
            'currency' => 'usd',
            'recurring' => [
                'interval' => 'month',
            ],
            'metadata' => [
                'plan_key' => $key,
            ],
        ]);

        echo "✅ Price created: {$price->id} (${$productData['price']}/month)\n";

        $priceIds[$key] = $price->id;

        echo "\n";

    } catch (Exception $e) {
        echo "❌ Error creating {$key}: " . $e->getMessage() . "\n\n";
    }
}

// Display results and .env update instructions
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ SETUP COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📝 Add these Price IDs to your .env file:\n\n";

foreach ($priceIds as $key => $priceId) {
    echo "STRIPE_PRICE_" . strtoupper($key) . "={$priceId}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔗 View your products in Stripe Dashboard:\n";
echo "https://dashboard.stripe.com/products\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 Next steps:\n";
echo "1. Copy the price IDs above and update your .env file\n";
echo "2. Set up Stripe webhook at: https://dashboard.stripe.com/webhooks\n";
echo "   - Endpoint URL: https://www.restaurantesmexicanosfamosos.com/stripe/webhook\n";
echo "   - Events: checkout.session.completed, customer.subscription.*\n";
echo "3. Copy the webhook signing secret to STRIPE_WEBHOOK_SECRET in .env\n";
echo "4. Run: php artisan config:clear\n\n";
