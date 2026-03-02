<?php

namespace Database\Seeders;

use App\Models\SubscriptionBenefit;
use Illuminate\Database\Seeder;

class SubscriptionBenefitsSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            // FREE TIER
            [
                'tier' => 'free',
                'business_code' => 'mf_imports',
                'business_name' => 'MF Imports',
                'business_url' => 'https://mf-imports.com',
                'business_logo' => '/images/logos/mf-imports.png',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'description' => '5% de descuento en tu compra',
                'sort_order' => 1,
            ],
            [
                'tier' => 'free',
                'business_code' => 'tormex',
                'business_name' => 'Tormex Pro',
                'business_url' => 'https://tormexpro.com',
                'business_logo' => '/images/logos/tormex.png',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'description' => '5% de descuento en tu compra',
                'sort_order' => 2,
            ],
            [
                'tier' => 'free',
                'business_code' => 'mf_trailers',
                'business_name' => 'MF Trailers',
                'business_url' => 'https://mftrailers.com',
                'business_logo' => '/images/logos/mf-trailers.png',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'description' => '5% de descuento en tu compra',
                'sort_order' => 3,
            ],
            [
                'tier' => 'free',
                'business_code' => 'muebles_mexicanos',
                'business_name' => 'Muebles Mexicanos',
                'business_url' => 'https://mueblesmexicanos.com',
                'business_logo' => '/images/logos/muebles-mexicanos.png',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'description' => '5% de descuento en tu compra',
                'sort_order' => 4,
            ],
            [
                'tier' => 'free',
                'business_code' => 'refrimex',
                'business_name' => 'Refrimex Paletería',
                'business_url' => 'https://refrimexpaleteria.com',
                'business_logo' => '/images/logos/refrimex.png',
                'discount_type' => 'fixed',
                'discount_value' => 5,
                'description' => '$5 de descuento en tu compra',
                'sort_order' => 5,
            ],

            // PREMIUM TIER
            [
                'tier' => 'premium',
                'business_code' => 'mf_imports',
                'business_name' => 'MF Imports',
                'business_url' => 'https://mf-imports.com',
                'business_logo' => '/images/logos/mf-imports.png',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'description' => '10% de descuento en tu compra',
                'sort_order' => 1,
            ],
            [
                'tier' => 'premium',
                'business_code' => 'tormex',
                'business_name' => 'Tormex Pro',
                'business_url' => 'https://tormexpro.com',
                'business_logo' => '/images/logos/tormex.png',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'description' => '10% de descuento en tu compra',
                'sort_order' => 2,
            ],
            [
                'tier' => 'premium',
                'business_code' => 'mf_trailers',
                'business_name' => 'MF Trailers',
                'business_url' => 'https://mftrailers.com',
                'business_logo' => '/images/logos/mf-trailers.png',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'description' => '10% de descuento en tu compra',
                'sort_order' => 3,
            ],
            [
                'tier' => 'premium',
                'business_code' => 'muebles_mexicanos',
                'business_name' => 'Muebles Mexicanos',
                'business_url' => 'https://mueblesmexicanos.com',
                'business_logo' => '/images/logos/muebles-mexicanos.png',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'description' => '10% de descuento en tu compra',
                'includes_free_shipping' => true,
                'sort_order' => 4,
            ],
            [
                'tier' => 'premium',
                'business_code' => 'refrimex',
                'business_name' => 'Refrimex Paletería',
                'business_url' => 'https://refrimexpaleteria.com',
                'business_logo' => '/images/logos/refrimex.png',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'description' => '10% de descuento en tu compra',
                'sort_order' => 5,
            ],

            // ELITE TIER
            [
                'tier' => 'elite',
                'business_code' => 'mf_imports',
                'business_name' => 'MF Imports',
                'business_url' => 'https://mf-imports.com',
                'business_logo' => '/images/logos/mf-imports.png',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'description' => '15% de descuento + envío gratis',
                'includes_free_shipping' => true,
                'sort_order' => 1,
            ],
            [
                'tier' => 'elite',
                'business_code' => 'tormex',
                'business_name' => 'Tormex Pro',
                'business_url' => 'https://tormexpro.com',
                'business_logo' => '/images/logos/tormex.png',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'description' => '15% de descuento + envío gratis',
                'includes_free_shipping' => true,
                'sort_order' => 2,
            ],
            [
                'tier' => 'elite',
                'business_code' => 'mf_trailers',
                'business_name' => 'MF Trailers',
                'business_url' => 'https://mftrailers.com',
                'business_logo' => '/images/logos/mf-trailers.png',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'description' => '15% de descuento',
                'sort_order' => 3,
            ],
            [
                'tier' => 'elite',
                'business_code' => 'muebles_mexicanos',
                'business_name' => 'Muebles Mexicanos',
                'business_url' => 'https://mueblesmexicanos.com',
                'business_logo' => '/images/logos/muebles-mexicanos.png',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'description' => '15% de descuento + envío gratis',
                'includes_free_shipping' => true,
                'sort_order' => 4,
            ],
            [
                'tier' => 'elite',
                'business_code' => 'refrimex',
                'business_name' => 'Refrimex Paletería',
                'business_url' => 'https://refrimexpaleteria.com',
                'business_logo' => '/images/logos/refrimex.png',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'description' => '15% de descuento',
                'sort_order' => 5,
            ],
        ];

        foreach ($benefits as $benefit) {
            SubscriptionBenefit::updateOrCreate(
                [
                    'tier' => $benefit['tier'],
                    'business_code' => $benefit['business_code'],
                ],
                $benefit
            );
        }

        $this->command->info('Subscription benefits seeded: ' . count($benefits) . ' records');
    }
}
